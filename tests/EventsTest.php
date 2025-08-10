<?php
declare(strict_types=1);

namespace Charcoal\Base\Tests;

use Charcoal\Base\Enums\ExceptionAction;
use Charcoal\Base\Events\AbstractEvent;
use Charcoal\Base\Events\Exception\EventListenerErrorException;
use Charcoal\Base\Tests\Fixtures\Events\EventBookOne;
use Charcoal\Base\Tests\Fixtures\Events\EventBookTyped;
use PHPUnit\Framework\TestCase;

/**
 * This test suite is designed to verify the functionality of various event registry implementations
 * and their associated capabilities, including instance memory isolation, accurate callback arguments,
 * and event serialization/deserialization.
 */
class EventsTest extends TestCase
{
    // Todo: test serialize and unserialize for both Event class and AbstractEventsRegistry class

    /**
     * This test verifies that if the registry's 'onListenerError' policy is set to THROW,
     * any exception thrown by the event listener is re-thrown as an EventListenerErrorException.
     * @return void
     * @throws EventListenerErrorException
     */
    public function testThrowExceptionOnListenerError(): void
    {
        // Prepare an event with a single throwing listener.
        $event = $this->prepareEventWithErrorListeners();
        // Configure the event's registry to throw on listener error.
        $event->registry->onListenerError = ExceptionAction::Throw;

        // Expect the triggered exception to be wrapped as EventListenerErrorException.
        $this->expectException(EventListenerErrorException::class);

        // Trigger the event. The listener will throw an exception that will be re-thrown.
        $event->trigger([]);
    }

    /**
     * This test ensures that if 'onListenerError' is set to IGNORE,
     * the exception is not re-thrown, and the event continues.
     * @return void
     * @throws EventListenerErrorException
     */
    public function testIgnoreExceptionOnListenerError(): void
    {
        // Create an event with multiple throwing listeners.
        $listenersCount = 3;
        $event = $this->prepareEventWithErrorListeners($listenersCount);
        // Configure the event's registry to ignore errors from listeners.
        $event->registry->onListenerError = ExceptionAction::Ignore;

        // Since we ignore exceptions, expect the trigger to run all the listeners without throwing.
        $this->assertEquals(
            $listenersCount,
            $event->trigger([]),
            "When ignoring errors, all listeners should be executed successfully"
        );
    }

    /**
     * This test shows that even if the registry is set to IGNORE,
     * we can still override this behavior by specifying THROW as a parameter to trigger().
     * @return void
     * @throws EventListenerErrorException
     */
    public function testThrowExceptionOverride(): void
    {
        // Prepare an event with a single throwing listener.
        $event = $this->prepareEventWithErrorListeners();
        // The registry default is set to IGNORE.
        $event->registry->onListenerError = ExceptionAction::Ignore;

        // We expect an exception because we override the ignored policy with THROW at trigger time.
        $this->expectException(EventListenerErrorException::class);

        // Trigger with an explicit THROW policy to override the default ignored behavior.
        $event->trigger([], ExceptionAction::Throw);
    }

    /**
     * This test confirms that if a listener throws a generic exception, it is caught and wrapped in
     * EventListenerErrorException with references to the original exception and event.
     */
    public function testEventListenerExceptionIsWrapped(): void
    {
        try {
            // Trigger an event that has throwing listeners. Uses the default 'Throw' policy.
            $this->prepareEventWithErrorListeners()->trigger([]);
        } catch (\Exception $e) {
            // Verify the exception is of the type EventListenerErrorException.
            $this->assertInstanceOf(
                EventListenerErrorException::class,
                $e,
                "Caught exception should be an instance of EventListenerErrorException"
            );

            // Check that the exception has a reference to the correct event object.
            $this->assertInstanceOf(
                AbstractEvent::class,
                $e->event,
                "The wrapped exception should reference the event instance that triggered it"
            );
            $this->assertInstanceOf(
                EventBookOne::class,
                $e->event->registry,
                "The event's registry should be an instance of EventBookOne"
            );

            // Verify the original exception is a RuntimeException with a specific message.
            $previous = $e->getPrevious();
            $this->assertInstanceOf(
                \RuntimeException::class,
                $previous,
                "The original exception should be a RuntimeException"
            );
            $this->assertEquals(
                "Exception from listener 1",
                $previous->getMessage(),
                "Original listener message should match 'Exception from listener 1'"
            );
        }
    }

    /**
     * This helper method creates an event (using EventBookOne) and registers the specified number of
     * throwing listeners. It then validates the setup before returning the event for testing.
     * @param int $count
     * @return AbstractEvent
     */
    private function prepareEventWithErrorListeners(int $count = 3): AbstractEvent
    {
        // Create the event object with a unique name based on this method name.
        $event = (new EventBookOne())->getEvent(__METHOD__);
        // Clear any existing listeners, ensuring a fresh start.
        $event->purgeListeners();

        // Confirm there are no listeners after purging.
        $this->assertEquals(
            0,
            $event->countListeners(),
            "The event should have 0 listeners after purging"
        );

        // Attach multiple throwing listeners.
        for ($i = 0; $i < $count; $i++) {
            $event->listen(function () use ($i) {
                throw new \RuntimeException("Exception from listener " . ($i + 1));
            }, uniqid("listener_" . $event->name . $i));
        }

        // Verify we attached the expected number of listeners.
        $this->assertEquals(
            $count,
            $event->countListeners(),
            "After registering $count listeners, the event should have the same count"
        );

        return $event;
    }

    /**
     * Tests the memory isolation of event registry instances.
     */
    public function testIndependentInstanceMemory(): void
    {
        // Create two instances of the same class, plus a different typed variant.
        $events1a = new EventBookOne();
        $events1b = new EventBookOne();
        $events2 = new EventBookTyped();

        // Verify that $events2 starts with a predefined count
        $this->assertEquals(
            3,
            $events2->getCount(),
            "Expected 'events2' to have 3 events registered initially"
        );

        // Verify the newly created $events1a starts with zero events
        $this->assertEquals(
            0,
            $events1a->getCount(),
            "A new 'events1a' instance should have 0 events registered upon creation"
        );

        // Define a new event name
        $eventName = "some-event";

        // Attach the event to $events1a
        $eventId1 = spl_object_id($events1a->on($eventName));

        // Check that $events1a now has exactly one event
        $this->assertEquals(
            1,
            $events1a->getCount(),
            "After registering a single event, 'events1a' should have exactly 1 event"
        );

        // Ensure $events1b is unaffected by changes made to $events1a
        $this->assertEquals(
            0,
            $events1b->getCount(),
            "'events1b' should still have 0 events, as it is a separate instance"
        );

        // Confirm $events2 has an event unique to it, not found in $events1a or $events1b
        $this->assertTrue(
            $events2->hasEvent($events2->event1),
            "Expected 'events2' to already have a specific predefined event"
        );
        $this->assertFalse(
            $events1a->hasEvent($events2->event1),
            "'events1a' must not find the event that belongs only to 'events2'"
        );
        $this->assertFalse(
            $events1b->hasEvent($events2->event1),
            "'events1b' must not find the event that belongs only to 'events2'"
        );

        // Verify that the new event name is only in $events1a (not in $events2, not in $events1b)
        $this->assertFalse(
            $events2->hasEvent($eventName),
            "Event 'some-event' should not exist in 'events2'"
        );
        $this->assertTrue(
            $events1a->hasEvent($eventName),
            "Event 'some-event' should be found in 'events1a'"
        );
        $this->assertFalse(
            $events1b->hasEvent($eventName),
            "'events1b' should not yet have the 'some-event' registered"
        );

        // Now register the same event name in $events1b
        $eventId2 = spl_object_id($events1b->on($eventName));

        // Both $events1a and $events1b should have one event each
        $this->assertEquals(
            1,
            $events1a->getCount(),
            "'events1a' must still have exactly 1 event"
        );
        $this->assertEquals(
            1,
            $events1b->getCount(),
            "'events1b' must now have exactly 1 event after the new registration"
        );

        // The two event IDs should differ, as they belong to separate instances
        $this->assertNotEquals(
            $eventId1,
            $eventId2,
            "Even though 'events1a' and 'events1b' share the same event name, they must have different event objects"
        );
    }

    /**
     * Tests the functionality of callback arguments provided to event listeners.
     * @throws EventListenerErrorException
     */
    public function testCallbackArguments(): void
    {
        // Create a new event registry instance
        $eventBook = new EventBookOne();

        // Register an event named "some-event" and capture its object ID
        $eventObject = $eventBook->on("some-event");
        $eventInstanceId = spl_object_id($eventObject);

        // Register another event with a different name, ensuring it has a different event object ID
        $this->assertNotEquals(
            $eventInstanceId,
            spl_object_id($eventBook->on("some-other-event")),
            "A second event with a different name must have a different object ID"
        );

        // Re-register the same "some-event" name, expecting the same object to be returned
        $this->assertEquals(
            $eventInstanceId,
            spl_object_id($eventBook->on("some-event")),
            "Re-registering an existing event name should return the same event object"
        );

        // Attach a listener to the event that checks its arguments at trigger time
        $eventBook->on($eventObject)->listen(function (string $msg, int $code, mixed $event) {
            // This callback is invoked when the event triggers
            $this->assertEquals(
                "Simple string match",
                $msg,
                "Listener argument #1 should be a specific string"
            );
            $this->assertEquals(
                0xffff,
                $code,
                "Listener argument #2 should be the hexadecimal 65535"
            );
            $this->assertInstanceOf(
                AbstractEvent::class,
                $event,
                "Listener argument #3 should be an Event object"
            );
        }, uniqid("event_" . $eventObject->name));

        // Trigger the event with the arguments to be verified by the above listener
        $eventObject->trigger(["Simple string match", 65535]);
    }
}