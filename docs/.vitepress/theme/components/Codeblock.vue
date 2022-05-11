<template>
  <div>
    <tabs :options="{ useUrlFragment: false }">
      <tab :id="anId" name="Annotations">
        <slot name="ann"></slot>
      </tab>
      <tab :id="atId" name="Attributes">
        <slot name="att"></slot>
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
        return 'ann-' + this.id
      },
      atId() {
        return 'att-' + this.id
      }
    }
  }
</script>
