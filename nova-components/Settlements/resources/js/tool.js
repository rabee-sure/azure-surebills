Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'settlements',
      path: '/settlements',
      component: require('./components/Index'),
    },
    {
      name: 'create-settlement',
      path: '/settlements/:id/create',
      component: require('./components/Create'),
    },
  ])
})
