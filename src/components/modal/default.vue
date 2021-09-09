<template>

	<k-dialog
		size="medium"
		class="k-pages-dialog"
		v-bind:visible="modal"
		v-on:cancel="modal_close"
		v-on:submit="modal_submit">
		<header
			class="k-pages-dialog-navbar">
			<k-button
				icon="angle-left"
	            v-on:click="nav_fetch(nav_breadcrumbs)"
				v-if="query.breadcrumbs.length > 0">
	            Back
	        </k-button>
			<k-headline>
				Add Pages
			</k-headline>
		</header>
		<k-list>
			<listModal
				v-for="(item, index) in query.content"
				v-bind:key="item.uuid"
				v-bind:item="item">
				<template
					v-slot:text>
					<span
						class="k-menu-text">
						{{ item.text }}
					</span>
				</template>
				<template
					v-slot:fetch>
					<k-button
						icon="angle-right"
						v-if="item.count > 0"
						v-on:click="nav_fetch(item.id)">
					</k-button>
				</template>
				<template
					v-slot:add>
					<k-button
						icon="add"
						v-on:click="nav_add(item)">
					</k-button>
				</template>
			</listModal>
		<k-list>
	</k-dialog>

</template>

<script>

	import listModal from '../lists/modal.vue'

	export default {
		props: {
			navigation: Array,
			modal: Boolean,
			endpoints: Object,
		},
		components: {
			listModal
		},
		data() {
			return {
				query: {
					content: [],
					breadcrumbs: []
				}
			}
		},
		methods: {
			modal_close() {
				this.$emit('modal_close', 'default')
			},
			modal_submit() {
				this.$emit('modal_submit', 'default')
			},
			nav_add(data) {
				this.navigation.push(JSON.parse(JSON.stringify(Object.assign(data, { uuid: Math.random().toString(36).substring(2, 15) }))))
			},
			nav_fetch(data) {
				this.$api.get(this.endpoints.field + '/listings/' + data)
	            .then((response) => {
					this.query = response
	            })
	            .catch(function (error) {
	                console.log(error);
	            })
			}
		},
		computed: {
			nav_breadcrumbs() {
				return this.query.breadcrumbs.length >= 2 ? this.query.breadcrumbs[this.query.breadcrumbs.length-2].id : 'site'
			}
		},
		mounted() {
			this.nav_fetch('site')
		}
	}

</script>

<style lang="scss" scoped>

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
