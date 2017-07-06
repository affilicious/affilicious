let product = Backbone.Model.extend({
    defaults: {
        title: '#'
    },

    parse(response){
        console.log(response);
        return response;
    }
});

export default product;
