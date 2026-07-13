<template>
  <div>
    <tabs :options="{ useUrlFragment: false }">
      <tab :id="atId" name="Attributes">
        <slot name="at"></slot>
      </tab>
      <tab :id="anId" name="Annotations">
        <slot name="an"></slot>
      </tab>
      <tab v-if="hasSpec" :id="specId" name="🧪 Spec Attributes">
        <slot name="spec"></slot>
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
    },
    specId() {
      return this.id + '-spec'
    },
    hasSpec() {
      return !!this.$slots.spec
    }
  }
}
</script>
