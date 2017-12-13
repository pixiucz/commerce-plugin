<template>
  <component :is="$store.state.sidebarRoute" />
</template>

<script>
  export default {
    name: 'sidebar',
  };
</script>
