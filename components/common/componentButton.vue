<template lang="pug">
.component-button(
  v-ripple
  )
  v-icon.icon(
    @click="$emit('clicked', id)"
    ) {{ icon }}
</template>

<script>
export default {
  props: {
    id: {
      type: String,
      require: false,
      default: null,
    },
    icon: {
      type: String,
      require: false,
      default: 'mdi-magnify',
    },
  },
  emits: ['clicked'],
}
</script>

<style lang="scss" scoped>
.component-button {
  position: fixed;
  bottom: calc(1.3em + 5em + var(--safe-area-inset-bottom));
  right: 1.3em;
  z-index: 50;
  height: 4em;
  width: 4em;
  background-color: var(--accent-color);
  color: white;
  border-radius: 9999px;
  .icon {
    font-size: 2.5em;
    height: 100%;
    width: 100%;
    cursor: pointer;
    overflow: hidden;
  }
}
</style>
