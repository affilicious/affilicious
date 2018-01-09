/**
 * The view of the tags field used for Carbon Fields.
 *
 * @since 0.8.16
 */
let Tags = carbon.fields.View.extend({

    /**
     * Initialize the tags view.
     *
     * @since 0.8.16
     * @public
     */
    initialize() {
        carbon.fields.View.prototype.initialize.apply(this);

        this.on('field:rendered', this._initTags);
        this.listenTo(this.model, 'change:visible', this._initTags);
    },

    /**
     * Initialize the tags with Selectize.
     *
     * @since 0.9.18
     * @private
     */
    _initTags() {
        let tags = this.model.get('tags');
        let maxItems = this.model.get('maxItems');

        if(tags) {
            this._initPredefinedTags(tags, maxItems);
        } else {
            this._initCustomTags(maxItems);
        }
    },

    /**
     * Initialize the predefined tags with auto-completion.
     *
     * @since 0.9.18
     * @private
     * @param {array} tags
     * @param {int} maxItems
     */
    _initPredefinedTags(tags, maxItems) {
        let select = this.$el.find('.aff-tags-predefined');
        let selectize = select[0].selectize;
        let options = Object.keys(tags).map((key) => {
            return {id: key, text: tags[key]}
        });

        if(!selectize) {
            select.selectize({
                plugins: ['drag_drop', 'remove_button'],
                delimiter: ',',
                persist: true,
                create: true,
                valueField: 'id',
                labelField: 'text',
                maxOptions: 10,
                searchField: ['text'],
                maxItems: maxItems,
                options: options,
                onChange: () => this._onChangeTags(),
            });
        } else {
            selectize.enable();
        }
    },

    /**
     * Initialize the custom tags without auto-completion.
     *
     * @private
     * @since 0.9.18
     * @param {int} maxItems
     */
    _initCustomTags(maxItems) {
        let select = this.$el.find('.aff-tags-custom');
        let selectize = select[0].selectize;

        if(!selectize) {
            select.selectize({
                plugins: ['drag_drop', 'remove_button'],
                delimiter: ',',
                persist: true,
                maxItems: maxItems,
                create: true,
                onChange: () => this._onChangeTags(),
            });
        } else {
            selectize.enable();
        }
    },

    /**
     * Set the new value of the model if the tags have changed.
     *
     * @since 0.9.18
     * @private
     */
    _onChangeTags() {
        let input = this.$el.find('input[name="' + this.templateVariables.name + '"]');
        this.model.set('value', input.val());
    }
});

export default Tags;
