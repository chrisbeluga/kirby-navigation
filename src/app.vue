<template>

	<k-field
        class="k-form-field navigation-field"
        v-bind:help="help"
		v-bind:label="label"
		v-bind:levels="levels"
		v-bind:disabled="disabled"
        v-bind:required="required">
		<template
            v-slot:options>
			<k-dropdown>
		        <k-button
		            icon="add"
		            v-on:click="$refs.menu.toggle()">
		            Add
		        </k-button>
		        <k-dropdown-content
		            ref="menu"
		            align="right">
					<k-dropdown-item
						v-on:click="modal_open('default')">
						<span
							class="k-menu-title">
							Kirby Link
						</span>
						<p
							class="k-menu-subtitle">
							Adds a Kirby page to the menu
						<p>
		            </k-dropdown-item>
					<k-dropdown-item
						v-on:click="modal_open('custom')">
						<span
							class="k-menu-title">
							Custom Link
						</span>
						<p
							class="k-menu-subtitle">
							Adds a custom link to the menu, useful external urls etc
						<p>
		            </k-dropdown-item>
		        </k-dropdown-content>
		    </k-dropdown>
        </template>
        <vue-nestable
            keyProp="uuid"
			v-model="navigation"
            childrenProp="children"
			v-if="navigation.length"
			v-bind:maxDepth="computed_levels">
			<template
				slot-scope="{ item, index }"
				v-bind:item="item">
				<listDefault
					v-bind:item="item"
					v-bind:navigation="navigation">
					<template
						v-slot:handle
						v-bind:item="item">
						<VueNestableHandle
							v-bind:item="item">
							<k-button
								icon="sort"
								tooltip="Sort Items"
								class="input-handle">
							</k-button>
						</VueNestableHandle>
					</template>
					<template
		                v-slot:dropdown_column_one>
						<k-text-field
							label="Link Text"
							v-model="item.text">
						</k-text-field>
						<k-text-field
							label="Link Title"
							v-model="item.title">
						</k-text-field>
						<k-toggle-field
							label="Popup"
							v-model="item.popup">
						</k-toggle-field>
		            </template>
					<template
		                v-slot:dropdown_column_two>
						<k-text-field
							label="Link ID"
							v-model="item.id">
						</k-text-field>
						<k-text-field
							label="Link Url"
							v-model="item.url">
						</k-text-field>
		            </template>
				</listDefault>
            </template>
        </vue-nestable>
		<k-empty
			v-else
			icon="page">
			No menu items yet
		</k-empty>
		<modalDefault
			v-if="modal.status"
			v-bind:modal="modal.status"
			v-on:modal_close="modal_close"
			v-on:modal_submit="modal_submit">
			<template
				v-slot:modal_header>
				<header
					class="k-pages-dialog-navbar">
					<template
						v-if="modal.type === 'default'">
						<k-button
							icon="angle-left"
							v-on:click="modal_fetch(computed_breadcrumbs)"
							v-if="query.breadcrumbs.length > 0">
				            Back
				        </k-button>
						<k-headline>
							Add Pages
						</k-headline>
					</template>
					<template
						v-else>
						<k-headline>
							Add Custom Link
						</k-headline>
					</template>
				</header>
			</template>
			<template
				v-slot:modal_body>
				<template
					v-if="modal.type === 'default'">
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
									v-on:click="modal_fetch(item.id)">
								</k-button>
							</template>
							<template
								v-slot:add>
								<k-button
									icon="add"
									v-on:click="modal_add(item)">
								</k-button>
							</template>
						</listModal>
					<k-list>
				</template>
				<template
					v-else>
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
				</template>
			</template>
		</modalDefault>
		<template
			v-slot:help>
			<k-grid>
				<k-column
					width="1/2">
					<k-text
						v-if="help"
						theme="help"
						class="k-field-help"
						v-html="help">
					</k-text>
				</k-column>
				<k-column
					width="1/2">
					<k-text
						theme="help"
						class="k-field-help k-field-depth">
						Maximum allowed depth: <strong>{{ computed_levels }}</strong>
					</k-text>
				</k-column>
			</k-grid>
		</template>
    </k-field>

</template>

<script>

    // Import Libraries
	import VueNestable from 'vue-nestable'

	// Import Components
	import listModal from './components/lists/modal.vue'
	import listDefault from './components/lists/default.vue'
	import modalDefault from './components/modal/default.vue'

    export default {
        props: {
			help: String,
			value: Array,
			label: String,
			levels: Number,
			disabled: Boolean,
			required: Boolean,
			endpoints: Object,
        },
        components: {
			listModal,
			listDefault,
			modalDefault,
        },
		data() {
			return {
				navigation: [],
				modal: { type: '', status: false },
				query: { content: [], breadcrumbs: [] },
				item: { url: '', text: '', popup: false }
			}
		},
        watch: {
            navigation: {
                handler() {
                    this.$emit('input', this.navigation)
                },
                deep: true
            }
        },
        methods: {
			modal_close() {
				this.modal = { type: '', status: false }
			},
			modal_open(data) {
				this.modal = { type: data, status: true }
			},
			modal_submit() {
				if(this.modal.type === 'custom') this.modal_add(this.item)
				this.modal = { type: '', status: false }
			},
			modal_add(data) {
				this.navigation.push(JSON.parse(JSON.stringify(Object.assign(data, { uuid: Math.random().toString(36).substring(2, 15) }))))
			},
			modal_fetch(data) {
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
			computed_navigation() {
				return this.navigation
			},
			computed_levels() {
				return this.levels ? this.levels : 10
			},
			computed_breadcrumbs() {
				return this.query.breadcrumbs.length >= 2 ? this.query.breadcrumbs[this.query.breadcrumbs.length-2].id : 'site'
			}
		},
		beforeCreate() {
			window.panel.app.$root.constructor.use(VueNestable)
		},
		mounted() {
			this.navigation = this.value
			this.modal_fetch('site')
		}
    }

</script>

<style lang="scss">

.navigation-field {

	.k-field {
		margin-bottom: 14px;
	}

	.k-field-depth {
		text-align: right;

		strong {
			color: #000;
		}
	}

	.k-field-header {

		.k-dropdown-content {
			margin-top: 10px;
		}

		.k-dropdown-item {
			width: 180px;
			margin-bottom: 10px;

			.k-button-text {
				opacity: 1;
				white-space: normal;
				text-align: left;

				.k-menu-title {
					opacity: 1;
					color: #fff;
					width: 100%;
					display: block;
					margin-bottom: 8px;
				}

				.k-menu-subtitle {
					opacity: 0.75;
					font-size: .675rem;
					line-height: 0.875rem;
				}
			}
		}
	}

	.k-list .k-list-item:not(:last-child) {
		margin-bottom: 2px;
	}

	.nestable-handle {
        width: 100%;
        display: block;
    }

    .nestable-item-content {
        width: 100%;
        display: flex;
        flex-wrap: nowrap;
        position: relative;
        align-items: center;

		&:hover {
			.nestable-handle {
				.k-button {
					opacity: 1;
					transition: all 0.3s ease-in-out;
				}
			}
		}
    }

    .nestable-handle {
		width: 26px;
		height: 100%;
        display: flex;
        flex-wrap: nowrap;
        position: relative;
        align-items: flex-start;
        margin-top: 7px;

		.k-button {
			opacity: 0.2;
			cursor: move;
			transition: all 0.3s ease-in-out;
		}
    }

    .nestable {
      position: relative;
    }
    .nestable .nestable-list {
      margin: 0;
      padding: 0 0 0 26px;
      list-style-type: none;
    }
    .nestable > .nestable-list {
      padding: 0;
    }
    .nestable-item:first-child,
    .nestable-item-copy:first-child {
      margin-top: 0;
    }
    .nestable-item {
      position: relative;
    }
    .nestable-item.is-dragging .nestable-list {
      pointer-events: none;
    }
    .nestable-item.is-dragging * {
      opacity: 0;
    }
    .nestable-item.is-dragging:before {
      content: '';
      position: absolute;
      top: 0;
      left: 26px;
      right: 0;
      bottom: 0;
      background: #e6e6e6;
	  transition: all 0.3s ease-in-out;
    }
    .nestable-drag-layer {
      position: fixed;
      top: 0;
      left: 0;
      z-index: 100;
      pointer-events: none;
    }
    .nestable-rtl .nestable-drag-layer {
      left: auto;
      right: 0;
    }
    .nestable-handle {
	  cursor: move;
    }

}
</style>
