<template>
    <div class="form-field-fieldtype-wrapper">
        <v-select
            v-if="showFieldtype && form"
            v-model="selected"
            :clearable="true"
            :options="fields"
            :reduce="(option) => option.id"
            :searchable="true"
            @input="$emit('input', $event)"
            placeholder="Choose a form field..."
        />
    </div>
</template>

<script>
    export default {
        mixins: [Fieldtype],
        inject: ['storeName'],
        data() {
            return {
                selected: null,
                showFieldtype: true,
                fields: [],
            }
        },
        computed: {
            form() {
                let key = 'forms.' + this.row + '.form.0';
                return data_get(this.$store.state.publish[this.storeName].values, key);
            },
            row() {
                let matches = this.namePrefix.match(/\[(.*?)\]/);
                return matches[1];
            }
        },
        methods: {
            updateFields() {
                this.$axios
                    .get(cp_url(`/sendinblue/form-fields/${this.form}`))
                    .then(response => {
                        console.log('response_data', response.data);
                        this.fields = response.data;
                    });
            }
        },
        watch: {
            form(form) {
                this.showFieldtype = false;
                this.updateFields();
                this.$nextTick(() => this.showFieldtype = true);
            }
        },
        mounted() {
            this.selected = this.value;
            this.updateFields();
        }
    };
</script>
