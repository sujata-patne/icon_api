<?php

namespace VOP\Utils;

use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;

class UuidUtils {

    public static function uuid() {
        try {

            $uuid = Uuid::uuid4();
            return $uuid;
        } catch (UnsatisfiedDependencyException $e) {
            // Some dependency was not met. Either the method cannot be called on a
            // 32-bit system, or it can, but it relies on Moontoast\Math to be present.
            Logger::critical("Unable to generate Uuid: " . $e->getMessage());
            Logger::logException($e);
            return null;
        }
    }

}
