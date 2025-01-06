<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="create_bank_transfer_form_modal">
        <div class="modal-content max-w-[1000px] top-[10%]">
            <div class="modal-header">
                <h3 class="modal-title capitalize">Create New Bitrix Bank Transfer</h3>
                <button class="btn btn-xs btn-icon btn-light" data-modal-dismiss="true" @click="$emit('closeModal')">
                    <i class="ki-outline ki-cross" ></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <span class="badge badge-sm badge-danger badge-outline">Note: If you cannot find the account name make sure to add it first in Bitrix. Click &nbsp <a href="https://crm.cresco.ae/services/lists/39/view/0/?list_section_id=" target="_blank" class="btn btn-link"> here &nbsp </a> to add a new bank account in Bitrix.</span>
                </div>
                <div class="flex">
                    <div class="w-1/2">

                    </div>
                    <div class="w-1/2">

                    </div>
                </div>
            </div>
            <div class="modal-footer justify-end">
                <div class="flex gap-4">
                    <button class="btn btn-light" data-modal-dismiss="true" @click="$emit('closeModal')">
                        Cancel
                    </button>
                    <button
                        class="btn btn-primary"
                        @click="save"
                        :disabled="loading"
                    >
                        Create Bank Transfer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import bitrixHelperMixin from "../../../../mixins/bitrixHelperMixin.js";
import vSelect from "vue-select";
import 'vue-select/dist/vue-select.css';
export default {
    name: "create-bank-transfer-form-modal",
    props: ['obj', 'type', 'bitrix_bank_transfer_company_ids'],
    mixins: [bitrixHelperMixin, vSelect],
    data(){
        return {
            form: {

            },
            form_data: {
            },
            // crud_loading: false,
            // loading: false,
        }
    },
    methods: {
        save(){
            // this.crud_loading = true
            // axios({
            //     url: `/admin/acl/save/${this.obj_id}`,
            //     method: 'POST',
            //     data: this.form
            // }).then(response => {
            //     this.$emit('closeModal')
            // }).catch(error => {
            //     console.log(error)
            // }).finally(() => {
            //     this.crud_loading = false;
            // })
        },
    },
    async mounted() {
        if(this.obj && this.type){
            this.form = this.obj;
            let companyId = null;
            if(this.type === "purchaseInvoice"){
                // companyId = this.bitrix_bank_transfer_company_ids.find(item => item.purchase_invoice_company_id === this.obj.company_id).bank_transfer_company_id;
                let data = {
                    IBLOCK_TYPE_ID: "bitrix_processes",
                    IBLOCK_ID: 104,
                    FILTER: {
                        ID: this.obj.id
                    }
                }
                try {
                    // this.loading = true
                    const response = await this.getBitrixIBlockData('lists.element.get', data, 'POST');
                    console.log(response)
                } catch (error) {
                    // this.loading = false
                }
            }
            // else if (type === "cashRequest"){
            //     this.companyId = this.mapCashRequestCompanyToBankTransferCompany(request.company_id);
            //     this.getCashRequestById(request.id);
            // }
        }
    }
}
</script>

<style scoped>

</style>
