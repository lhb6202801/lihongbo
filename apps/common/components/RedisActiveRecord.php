<?php
/**
 * Created by PhpStorm.
 * User: limingming
 * Date: 17/4/19
 * Time: 下午1:06
 */
namespace  common\components;

class RedisActiveRecord extends \yii\redis\ActiveRecord
{
    public function insert($db = null, $runValidation = true, $attributes = null)
    {
        if ($runValidation && !$this->validate($attributes)) {
            return false;
        }
        if (!$this->beforeSave(true)) {
            return false;
        }
        if ($db == null) {
            $db = static::getDb();
        }
        $values = $this->getDirtyAttributes($attributes);
        $pk = [];
        foreach ($this->primaryKey() as $key) {
            $pk[$key] = $values[$key] = $this->getAttribute($key);
            if ($pk[$key] === null) {
                // use auto increment if pk is null
                $pk[$key] = $values[$key] = $db->executeCommand('INCR', [static::keyPrefix() . ':s:' . $key]);
                $this->setAttribute($key, $values[$key]);
            } elseif (is_numeric($pk[$key])) {
                // if pk is numeric update auto increment value
                $currentPk = $db->executeCommand('GET', [static::keyPrefix() . ':s:' . $key]);
                if ($pk[$key] > $currentPk) {
                    $db->executeCommand('SET', [static::keyPrefix() . ':s:' . $key, $pk[$key]]);
                }
            }
        }
        // save pk in a findall pool
        $db->executeCommand('RPUSH', [static::keyPrefix(), static::buildKey($pk)]);

        $key = static::keyPrefix() . ':a:' . static::buildKey($pk);
        // save attributes
        $setArgs = [$key];
        foreach ($values as $attribute => $value) {

            // only insert attributes that are not null
            if ($value !== null) {
                if (is_bool($value)) {
                    $value = (int) $value;
                }
                $setArgs[] = $attribute;
                $setArgs[] = $value;
            }
        }

        if (count($setArgs) > 1) {
            $db->executeCommand('HMSET', $setArgs);
        }

        $changedAttributes = array_fill_keys(array_keys($values), null);
        $this->setOldAttributes($values);
        $this->afterSave(true, $changedAttributes);

        return true;
    }

    public static function updateAll($attributes, $condition = null, $db = null)
    {
        if (empty($attributes)) {
            return 0;
        }
        if ($db == null) {
            $db = static::getDb();
        }
        $n = 0;
//var_dump($db);
//var_dump(self::fetchPks($condition, $db));
        foreach (self::fetchPks($condition, $db) as $pk) {
            $newPk = $pk;
            $pk = static::buildKey($pk);
            $key = static::keyPrefix() . ':a:' . $pk;
            // save attributes
            $delArgs = [$key];
            $setArgs = [$key];
            foreach ($attributes as $attribute => $value) {
                if (isset($newPk[$attribute])) {
                    $newPk[$attribute] = $value;
                }
                if ($value !== null) {
                    if (is_bool($value)) {
                        $value = (int) $value;
                    }
                    $setArgs[] = $attribute;
                    $setArgs[] = $value;
                } else {
                    $delArgs[] = $attribute;
                }
            }
            $newPk = static::buildKey($newPk);
            $newKey = static::keyPrefix() . ':a:' . $newPk;
            // rename index if pk changed
            if ($newPk != $pk) {
                $db->executeCommand('MULTI');
                if (count($setArgs) > 1) {
                    $db->executeCommand('HMSET', $setArgs);
                }
                if (count($delArgs) > 1) {
                    $db->executeCommand('HDEL', $delArgs);
                }
                $db->executeCommand('LINSERT', [static::keyPrefix(), 'AFTER', $pk, $newPk]);
                $db->executeCommand('LREM', [static::keyPrefix(), 0, $pk]);
                $db->executeCommand('RENAME', [$key, $newKey]);
                $db->executeCommand('EXEC');

                if (count($setArgs) > 1) {
                    $db->executeCommand('HMSET', $setArgs);
                }
                if (count($delArgs) > 1) {
                    $db->executeCommand('HDEL', $delArgs);
                }
            }
            $n++;
        }

        return $n;
    }

    public static function deleteAll($condition = null, $db = null)
    {
        if ($db == null) {
            $db = static::getDb();
        }
        $pks = self::fetchPks($condition, $db);
        if (empty($pks)) {
            return 0;
        }
        $attributeKeys = [];
        $db->executeCommand('MULTI');
        foreach ($pks as $pk) {
            $pk = static::buildKey($pk);
            $db->executeCommand('LREM', [static::keyPrefix(), 0, $pk]);
            $attributeKeys[] = static::keyPrefix() . ':a:' . $pk;
        }
        $db->executeCommand('DEL', $attributeKeys);
        $result = $db->executeCommand('EXEC');

        return end($result);
    }

    private static function fetchPks($condition, $db = null)
    {
        $query = static::find();
        $query->where($condition);
        $records = $query->asArray()->all($db); // TODO limit fetched columns to pk
        $primaryKey = static::primaryKey();

        $pks = [];
        foreach ($records as $record) {
            $pk = [];
            foreach ($primaryKey as $key) {
                $pk[$key] = $record[$key];
            }
            $pks[] = $pk;
        }

        return $pks;
    }
}
