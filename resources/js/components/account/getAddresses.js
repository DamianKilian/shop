export default {
    props: [
        'getAreaCodesUrl',
        'getAddressesUrl',
    ],
    data() {
        return {
            defaultAreaCode: null,
            areaCodes: [],
            addresses: [],
            getAddressesDone: false,
        };
    },
    methods: {
        clearAddress: function (address) {
            address.id = null;
            address.email = '';
            address.name = '';
            address.surname = '';
            address.nip = '';
            address.company_name = '';
            address.area_code_id = this.defaultAreaCode.id;
            address.phone = '';
            address.street = '';
            address.house_number = '';
            address.apartment_number = '';
            address.postal_code = '';
            address.city = '';
            return address;
        },
        getAddressById: function (id) {
            var address = null;
            _.forEach(this.addresses, function (addr) {
                if (id === addr.id) {
                    address = addr;
                    return false;
                }
            });
            return address;
        },
        getAddressesWithAreaCodes: function (getAddresses = true) {
            var that = this;
            return axios
                .post(this.getAreaCodesUrl)
                .then(function (response) {
                    that.areaCodes = response.data.areaCodes;
                    that.defaultAreaCode = response.data.defaultAreaCode;
                    that.clearAddress(that.address);
                    if(getAddresses){
                        that.getAddresses();
                    }
                }).catch(function (error) {
                    that.globalError = error.message;
                    console.log(error.message);
                });
        },
        getAddresses: function () {
            this.addresses = [];
            var that = this;
            axios
                .post(this.getAddressesUrl)
                .then(function (response) {
                    that.addresses = response.data.addresses;
                    that.arrangeAddresses();
                    that.getAddressesDone = true;
                })
                .catch(function (error) {
                    that.globalError = error.message;
                    console.log(error.message);
                });
        },
        arrangeAddresses: function () {
            var that = this;
            _.forEach(this.addresses, function (address) {
                address.selected = false;
            });
        },
    },
};