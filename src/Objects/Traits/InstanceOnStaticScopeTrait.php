<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Objects\Traits;

use Charcoal\Base\Objects\ObjectHelper;

/**
 * This trait enforces a singleton-like pattern at the class level. It binds exactly one instance to the
 * static scope of the "class hierarchy" where the trait is used. This means if a parent class uses the trait,
 * all of its subclasses share that same stored instance. Unrelated classes that each include the trait will
 * each hold their own independent instance. The method prevents re-initialization by throwing an exception
 * if an instance already exists, ensuring one consistent object reference per hierarchy chain.
 */
trait InstanceOnStaticScopeTrait
{
    private static ?self $instance = null;

    /**
     * Initializes the static instance with the given object.
     */
    final public static function initializeStatic(self $self): static
    {
        if (static::$instance) {
            throw new \DomainException(ObjectHelper::baseClassName(static::class) .
                " instance is already initialized on static scope");
        }

        return static::$instance = $self;
    }

    /**
     * Initializes and returns a new instance of the class if it has not already been initialized.
     */
    final public static function initialize(): static
    {
        if (static::$instance) {
            throw new \DomainException(ObjectHelper::baseClassName(static::class) .
                " instance is already initialized on static scope");
        }

        return static::$instance = new static(...func_get_args());
    }

    /**
     * Returns the existing instance of the class if it has been initialized.
     */
    final public static function getInstance(): static
    {
        if (!static::$instance) {
            throw new \DomainException(ObjectHelper::baseClassName(static::class) .
                " instance is not initialized on static scope");
        }

        return static::$instance;
    }

    /**
     * Retrieves the existing instance of the class or,
     * initializes a new one if it does not already exist.
     */
    protected static function getOrCreateInstance(): static
    {
        if (!static::$instance) {
            static::initialize();
        }

        return static::$instance;
    }
}