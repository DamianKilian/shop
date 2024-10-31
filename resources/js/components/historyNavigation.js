export default {
    mounted() {
        window.addEventListener("popstate", (e) => {
            if(!e.state){
                location.reload();
            } else {
                this.getProductsView(e.state, false, false);
            }
        });
    },
};
