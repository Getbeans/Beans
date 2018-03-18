class MyGameClock {

	/**
	 * Creates an instance of MyGameClock.
	 *
	 * @param maxTime Clock's timeout increment.
	 */
	constructor( maxTime ) {
		this.maxTime      = maxTime;
		this.currentClock = 0;
	}

	/**
	 * Get the remaining time on the clock.
	 *
	 * @returns {number}
	 */
	getRemainingTime() {
		return this.maxTime - this.currentClock;
	}
}