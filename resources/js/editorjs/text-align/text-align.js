import './text-align.scss';
import * as $ from './utils/dom';

/**
 * Available predefined variants
 */
export const AlignVariant = {
    Left: 'left',
    Center: 'center',
    Right: 'right',
    Justify: 'justify',
};


/**
 * This Block Tunes allows user to select some of predefined text align variant.
 *
 * @see AlignVariant enum for the details.
 * @uses Block Tunes API  {@link https://editorjs.io/block-tunes-api}
 */
export default class TextAlign {
    /**
     * Tune constructor. Called on Block creation
     *
     * @param options - constructor params
     * @param api - editor.js Core API
     * @param block - editor.js Block API
     * @param data - previously saved data
     * @param config - configuration supported by Tune
     */
    constructor({ api, data, config, block }) {
        this.api = api;
        this.data = data;
        this.config = config;
        this.block = block;

        this.variants = [
            {
                name: AlignVariant.Left,
                icon: '<i class="fa-solid fa-align-left"></i>',
                title: this.api.i18n.t('Left'),
            },
            {
                name: AlignVariant.Center,
                icon: '<i class="fa-solid fa-align-center"></i>',
                title: this.api.i18n.t('Center'),
            },
            {
                name: AlignVariant.Right,
                icon: '<i class="fa-solid fa-align-right"></i>',
                title: this.api.i18n.t('Right'),
            },
            {
                name: AlignVariant.Justify,
                icon: '<i class="fa-solid fa-align-justify"></i>',
                title: this.api.i18n.t('Justify'),
            },
        ];

        this.wrapper = undefined;
    }

    /**
     * Tell editor.js that this Tool is a Block Tune
     *
     * @returns {boolean}
     */
    static get isTune() {
        return true;
    }

    /**
     * CSS selectors used in Tune
     */
    static get CSS() {
        return {
            toggler: 'cdx-text-align__toggler',
        };
    }

    /**
     * Create Tunes controls wrapper that will be appended to the Block Tunes panel
     *
     * @returns {Element}
     */
    render() {
        const tuneWrapper = $.make('div', '');

        this.variants.forEach(({ name, icon, title }) => {
            const toggler = $.make('div', [this.api.styles.settingsButton, TextAlign.CSS.toggler], {
                innerHTML: icon,
            });

            toggler.dataset.name = name;

            this.api.tooltip.onHover(toggler, title, {
                placement: 'top',
                hidingDelay: 500,
            });

            tuneWrapper.appendChild(toggler);
        });

        /**
         * Delegate click event on all the controls
         */
        this.api.listeners.on(tuneWrapper, 'click', (event) => {
            this.tuneClicked(event);
        });

        return tuneWrapper;
    }

    /**
     * Handler for Tune controls click
     * Toggles the variant
     *
     * @param {MouseEvent} event - click
     * @returns {void}
     */
    tuneClicked(event) {
        const settingsButtonClass = `.${this.api.styles.settingsButton}`;
        const tune = event.target.closest(settingsButtonClass);
        const tunes = tune.parentElement.querySelectorAll(settingsButtonClass);
        tunes.forEach((t) => {
            t.classList.remove(this.api.styles.settingsButtonActive);
        });
        const isEnabled = tune.classList.contains(this.api.styles.settingsButtonActive);
        tune.classList.toggle(this.api.styles.settingsButtonActive, !isEnabled);
        this.variant = !isEnabled ? tune.dataset.name : '';
        this.block.dispatchChange();
    }

    /**
     * Wraps Block Content to the Tunes wrapper
     *
     * @param {Element} blockContent - editor.js block inner container
     * @returns {Element} - created wrapper
     */
    wrap(blockContent) {
        this.wrapper = $.make('div');

        this.variant = this.data;

        this.wrapper.appendChild(blockContent);

        return this.wrapper;
    }

    /**
     * Save current variant in memory and apply style for that
     *
     * @param {string} name - variant to save
     */
    set variant(name) {
        this.data = name;

        this.variants.forEach((variant) => {
            this.wrapper.classList.toggle(`cdx-text-align--${variant.name}`, variant.name === this.data);
        });
    }

    /**
     * Returns Tune state
     *
     * @returns {string}
     */
    save() {
        return this.data || '';
    }
}