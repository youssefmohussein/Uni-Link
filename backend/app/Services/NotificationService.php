<?php
namespace App\Services;

use App\Observers\NotificationObserver;

/**
 * NotificationService
 * 
 * Observer Pattern Implementation
 * Manages notification observers and broadcasts events
 * Part of the Notification System (UML Design)
 */
class NotificationService
{
    private array $observers = [];

    /**
     * Subscribe an observer to notifications
     */
    public function subscribe(NotificationObserver $observer): void
    {
        $this->observers[] = $observer;
    }

    /**
     * Unsubscribe an observer
     */
    public function unsubscribe(NotificationObserver $observer): void
    {
        $this->observers = array_filter($this->observers, function ($obs) use ($observer) {
            return $obs !== $observer;
        });
    }

    /**
     * Notify all subscribed observers of an event
     * 
     * @param string $eventType Type of event (e.g., 'POST_CREATED', 'CHAT_MENTION')
     * @param array $payload Event data
     */
    public function notifyAll(string $eventType, array $payload): void
    {
        error_log("NotificationService: notifyAll called with event: " . $eventType);
        error_log("NotificationService: Payload: " . json_encode($payload));
        error_log("NotificationService: Number of observers: " . count($this->observers));

        foreach ($this->observers as $index => $observer) {
            try {
                $observerClass = get_class($observer);
                error_log("NotificationService: Calling observer #" . $index . ": " . $observerClass);

                $observer->update($eventType, $payload);

                error_log("NotificationService: Observer #" . $index . " completed successfully");
            } catch (\Exception $e) {
                error_log("NotificationService: Observer failed - " . $e->getMessage());
                error_log("NotificationService: Stack trace: " . $e->getTraceAsString());
            }
        }

        error_log("NotificationService: notifyAll completed for event: " . $eventType);
    }

    /**
     * Get count of registered observers
     */
    public function getObserverCount(): int
    {
        return count($this->observers);
    }
}
