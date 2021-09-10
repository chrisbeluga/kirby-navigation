<template>

	<div
		class="k-form-input">
		<div
			class="k-form-inner">
			<slot
				name="text">
			</slot>
			<div
				class="k-form-actions">
				<slot
					name="fetch">
				</slot>
				<slot
					name="add">
				</slot>
			</div>
		</div>
	</div>

</template>

<script>

    export default {
        props: {
			item: Object,
        }
    }

</script>

<style lang="scss" scoped>

    .k-form-input {
        width: 100%;
        display: flex;
		background: #fff;
        position: relative;
		margin-bottom: 2px;

		.k-form-inner {
			width: 100%;
	        display: flex;
	        position: relative;
			align-items: center;
			justify-content: space-between;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
			align-items: baseline;
			width: 100%;
			line-height: 1.25rem;
			padding: .5rem .75rem;
		}

		.k-form-actions {
			display: flex;
			align-items: center;

			button {
				margin-left: 20px;
			}
		}
    }

</style>
