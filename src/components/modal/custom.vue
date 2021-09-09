<template>

	<k-dialog
		size="medium"
		class="k-pages-dialog"
		v-bind:visible="modal"
		v-on:cancel="modal_close"
		v-on:submit="modal_submit">
		<header
			class="k-pages-dialog-navbar">
			<k-headline>
				Add Custom Link
			</k-headline>
		</header>
		<k-grid>
			<k-column
				width="2/2">
				<k-text-field
					label="Link Text"
					v-model="item.text">
				</k-text-field>
				<k-text-field
					label="Link Url"
					v-model="item.url">
				</k-text-field>
				<k-toggle-field
					label="Popup"
					v-model="item.popup">
				</k-toggle-field>
			</k-column>
		</k-grid>
	</k-dialog>

</template>

<script>

	export default {
		props: {
			navigation: Array,
			modal: Boolean,
			endpoints: Object,
		},
		data() {
			return {
				item: {
					url: '',
					text: '',
					popup: false,
				}
			}
		},
		methods: {
			modal_close() {
				this.$emit('modal_close', 'custom')
			},
			modal_submit() {
				this.navigation.push(JSON.parse(JSON.stringify(Object.assign(this.item, { uuid: Math.random().toString(36).substring(2, 15) }))))
				this.$emit('modal_submit', 'custom')
			},
		},
	}

</script>

<style lang="scss" scoped>

	.k-field {
		margin-bottom: 1.4rem;
	}

	.k-pages-dialog-navbar {
		display: flex;
		align-items: center;
		justify-content: center;
		margin-bottom: 0.5rem;
		padding-right: 38px;
	}
	.k-pages-dialog-navbar .k-button {
		width: 38px;
	}
	.k-pages-dialog-navbar .k-button[disabled] {
		opacity: 0;
	}
	.k-pages-dialog-navbar .k-headline {
		flex-grow: 1;
		text-align: center;
	}
	.k-pages-dialog .k-list-item {
		cursor: pointer;
	}
	.k-pages-dialog .k-list-item .k-button[data-theme="disabled"],
	.k-pages-dialog .k-list-item .k-button[disabled] {
		opacity: 0.25;
	}
	.k-pages-dialog .k-list-item .k-button[data-theme="disabled"]:hover {
		opacity: 1;
	}

</style>
