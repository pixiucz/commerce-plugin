const ClosableSidebar = {
  methods: {
    closeSidebar() {
      this.$store.commit('SET_SIDEBAR_VISIBLE', false);
    },
    openSidebar(route) {
      this.$store.commit('SET_SIDEBAR_ROUTE', route);
      this.$store.commit('SET_SIDEBAR_VISIBLE', true);
    },
  },
};

export default ClosableSidebar;
