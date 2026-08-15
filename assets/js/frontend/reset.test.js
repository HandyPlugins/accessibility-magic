/* global beforeEach, describe, expect, it, jest */

import { patchReset } from './reset';

describe('patchReset', () => {
	let accessibility;

	beforeEach(() => {
		window.localStorage.clear();
		document.body.innerHTML = `
			<span data-init-word-spacing="1px" style="word-spacing: 3px"></span>
			<span data-init-letter-spacing="0.1px" style="letter-spacing: 2px"></span>
			<span data-init-letter-spacing="normal" style="letter-spacing: 4px"></span>
		`;

		accessibility = {
			body: document.body,
			menuInterface: {
				bigCursor: jest.fn(),
				disableAnimations: jest.fn(),
				grayHues: jest.fn(),
				invertColors: jest.fn(),
				readingGuide: jest.fn(),
				speechToText: jest.fn(),
				textToSpeech: jest.fn(),
				underlineLinks: jest.fn(),
			},
			onChange: jest.fn(function (updateSession) {
				if (updateSession) {
					window.localStorage.setItem(
						'_accessState',
						JSON.stringify(this.sessionState)
					);
				}
			}),
			resetIfDefined: jest.fn((source, destination, property) => {
				destination[property] = source;
			}),
			resetLineHeight: jest.fn(),
			resetTextSize: jest.fn(),
			sessionState: {
				textSpace: 2,
			},
			stateValues: {
				body: {
					letterSpacing: 'normal',
					wordSpacing: 'normal',
				},
			},
		};

		patchReset(accessibility);
	});

	it('skips the disabled animations module while completing reset', () => {
		accessibility.resetAll();

		expect(
			accessibility.menuInterface.disableAnimations
		).not.toHaveBeenCalled();
		expect(accessibility.menuInterface.invertColors).not.toHaveBeenCalled();
		expect(accessibility.resetTextSize).toHaveBeenCalled();
		expect(accessibility.resetLineHeight).toHaveBeenCalled();
		expect(JSON.parse(window.localStorage.getItem('_accessState'))).toEqual(
			{
				textSpace: 0,
			}
		);
	});

	it('resets animations when its menu button exists', () => {
		document.body.insertAdjacentHTML(
			'beforeend',
			'<div class="_access-menu"><button data-access-action="disableAnimations"></button></div>'
		);

		accessibility.resetAll();

		expect(
			accessibility.menuInterface.disableAnimations
		).toHaveBeenCalledWith(true);
	});

	it('restores word and letter spacing from their own saved values', () => {
		accessibility.resetTextSpace();

		const elements = document.querySelectorAll('span');
		expect(elements[0].style.wordSpacing).toBe('1px');
		expect(elements[1].style.letterSpacing).toBe('0.1px');
		expect(elements[2].style.letterSpacing).toBe('normal');
		expect(document.querySelector('[data-init-word-spacing]')).toBeNull();
		expect(document.querySelector('[data-init-letter-spacing]')).toBeNull();
	});
});
