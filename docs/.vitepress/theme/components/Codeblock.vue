<template>
  <div>
    <client-only>
      <tabs :options="{ useUrlFragment: false }">
        <tab :id="anId" name="Annotations">
          <slot name="an"></slot>
        </tab>
        <tab :id="atId" name="Attributes">
          <slot name="at"></slot>
        </tab>
      </tabs>
    </client-only>
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
        return 'an-' + this.id
      },
      atId() {
        return 'at-' + this.id
      }
    }
  }
</script>
