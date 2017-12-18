<template>
  <aside class="sidebar" :class="{'sidebar--shifted': $store.state.sidebar.show}">
    <component :is="$store.state.sidebar.route" />
  </aside>
</template>

<script>
  import Cart from './Cart';
  import User from './User';
  import Order from './Order';
  import Orders from './Orders';

  export default {
    name: 'sidebar',
    components: {
      Cart,
      User,
      Order,
      Orders,
    },
  };
</script>

<style lang="scss">
  @import '../../assets/style.scss';

  .sidebar {
    position: fixed;
    right: 0;
    transform: translateX($sidebar-width);
    top: 0;
    bottom: 0;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 0 15px 15px;
    color: #fff;
    background-color: #24272C;
    transition: $sidebar-transition;
    width: $sidebar-width;

    &--shifted {
      transform: translateX(0);
    }
  }
</style>
