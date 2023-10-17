CHANGELOG
=========

6.4
---

 * Mark the component as non experimental
 * [BC BREAK] Add `from()` to `CheckpointInterface`
 * Add `--date` and `--all` options to the `schedule:debug` command
 * Allow setting timezone of next run date in CronExpressionTrigger
 * Add `AbstractTriggerDecorator`
 * Make `ScheduledStamp` "send-able"
 * Add `ScheduledStamp` to `RedispatchMessage`
 * Allow modifying Schedule instances at runtime
 * Add `MessageProviderInterface` to trigger unique messages at runtime
 * Add `PreRunEvent` and `PostRunEvent` events
 * Add `DispatchSchedulerEventListener`
 * Add `FailureEvent` event

6.3
---

 * Add the component as experimental
