var vm = new Vue({
  delimiters: ['${', '}'],
  el: 'main#main',
  data: data,
  methods: {
    sectionChanged: function() {

      this.entryTypeId = 0;

      if(this.sectionId != 0)
      {
        this.entryTypeId = this.sections[this.sectionId].entryTypes[0].id;
      }

      this.updateMainCmsNavUrls();
      this.runQuery();

    },
    entryTypeChanged: function() {
      this.entryTypeId = $('select[name="entryTypeId"]:visible').val();
      this.updateMainCmsNavUrls();
      this.runQuery();
    },
    runQuery: _.debounce(function()
    {
      this.$nextTick(function () {
        runQuery($("div.filters"));
      });
    }, 100),
    updateMainCmsNavUrls: function()
    {
      this.$nextTick(function () {
        updateMainCmsNavUrls();
      });
    },
    toggleCustomDisplayTemplateField: function(id)
    {
      if(vm.openedCustomDisplayTemplateFields.indexOf(id) != -1)
      {
        vm.openedCustomDisplayTemplateFields = _.remove(vm.openedCustomDisplayTemplateFields, function (item) {
          return id != item;
        });
      }
      else
      {
        vm.openedCustomDisplayTemplateFields.push(id);
      }
    },
  },
  computed: {
    selectAll: {
      get: function () {
          return this.userGroups ? this.applyTo.length == this.userGroups.length : false;
      },
      set: function (value) {
          vm.applyTo = [];

          if (value)
          {
            vm.userGroups.forEach(function(group) {
              vm.applyTo.push(group.id);
            });
          }
      }
    }
  }
});

vm.sectionChanged();
vm.runQuery();