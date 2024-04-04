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
      this.runQuery();

    },
    entryTypeChanged: function() {
      this.entryTypeId = $('select[name="entryTypeId"]:visible').val();
      this.runQuery();
    },
    runQuery: _.debounce(function()
    {
      this.$nextTick(function () {
        runQuery($("div.filters"));
      });
    }, 0),
  },
  computed: {
    selectAll: {
      get: function() {
        return this.searchIds.length == this.savedSearches.length;
      },
      set: function (value) {
        searchIds = [];
        if(value)
        {
          this.savedSearches.forEach(function (savedSearch) {
            searchIds.push(savedSearch.id);
          });
        }
        this.searchIds = searchIds;
      }
    }
  }
});