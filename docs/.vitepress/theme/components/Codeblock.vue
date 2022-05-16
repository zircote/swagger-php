<template>
  <div>
    <tabs :options="{ useUrlFragment: false }">
      <tab :id="anId" name="Annotations">
        <slot name="an"></slot>
      </tab>
      <tab :id="atId" name="Attributes">
        <slot name="at"></slot>
      </tab>
    </tabs>
  </div>
</template>

<script>
export default {
  props: {
    id: {
      type: String,
      default: null
    }
  },
  computed: {
    anId() {
      return this.id + '-an'
    },
    atId() {
      return this.id + '-at'
    }
  }
}
</script>
