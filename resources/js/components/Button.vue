<template>

    <button class="btn btn-default btn-primary cursor-pointer"
         :class="{ 'btn-unselected': !state, 'btn-primary': state }">

        <slot/>

    </button>

</template>

<script>

    export default {
        props: {
            state: {
                type: Boolean,
                required: true
            }
        }
    }

</script>
