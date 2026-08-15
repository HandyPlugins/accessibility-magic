const RESETTABLE_MENU_ACTIONS = [
	'textToSpeech',
	'speechToText',
	'disableAnimations',
	'underlineLinks',
	'grayHues',
	'invertColors',
	'bigCursor',
	'readingGuide',
];

/**
 * Patch reset behavior without changing the bundled accessibility UI.
 *
 * @param {Object} accessibility Accessibility instance.
 */
export function patchReset(accessibility) {
	accessibility.resetTextSpace = function () {
		this.resetIfDefined(
			this.stateValues.body.wordSpacing,
			this.body.style,
			'wordSpacing'
		);
		this.resetIfDefined(
			this.stateValues.body.letterSpacing,
			this.body.style,
			'letterSpacing'
		);

		[
			{
				attribute: 'data-init-word-spacing',
				property: 'wordSpacing',
			},
			{
				attribute: 'data-init-letter-spacing',
				property: 'letterSpacing',
			},
		].forEach(({ attribute, property }) => {
			document.querySelectorAll(`[${attribute}]`).forEach((element) => {
				element.style[property] = element.getAttribute(attribute);
				element.removeAttribute(attribute);
			});
		});

		this.sessionState.textSpace = 0;
		this.onChange(true);
	};

	accessibility.resetAll = function () {
		RESETTABLE_MENU_ACTIONS.forEach((action) => {
			const selector = `._access-menu [data-access-action="${action}"]`;

			if (document.querySelector(selector)) {
				this.menuInterface[action](true);
			}
		});

		this.resetTextSize();
		this.resetTextSpace();
		this.resetLineHeight();
	};
}
