<template>
  <div>
    <div v-if="success">
      <b-container class="text-center" style="margin-top: 150px;">
        <h1>
          <i style="color: green;" class="el-icon-success"></i>
        </h1>
        <h1>
          Objednávka dokončena!
        </h1>
        <p>
          Podorobnosti naleznete v e-mailu, ktery Vám byl zaslán 
        </p>
        <router-link :to="{ name: 'home' }">
          <el-button type="warning">
            Vrátit se na hlavní stránku
          </el-button>
        </router-link>
      </b-container>
    </div>
    <div v-else v-loading="isLoading">
      <b-container style="margin-bottom: 75px;">
        <h1 class="mt-3"> Dokončení objednávky </h1>

        <div style="padding: 10px;">
          <el-steps class="mt-3" :active="step" finish-status="success" align-center>
            <el-step title="Obsah objednávky" icon="el-icon-info"></el-step>
            <el-step title="Zákazník" icon="el-icon-location"></el-step>
            <el-step title="Způsob platby / Dopravy" icon="el-icon-menu"></el-step>
            <el-step title="Shrnutí objednávky" icon="el-icon-star-on"></el-step>
          </el-steps>

          <hr>
          <div v-if="step === 3" class="">
            <b-row class="text-center">
              <b-col cols="3">
                <h5> Doručovací adresa </h5>
                <address-card :classProp="'no-shadow no-border'" :deletable="false" :address="this.addressForm"></address-card>
              </b-col>
              <b-col cols="3">
                <h5> Fakturační adresa </h5>
                <address-card v-if="!isBillingDifferent" :classProp="'no-shadow no-border'" :deletable="false" :address="this.addressForm"></address-card>
                <address-card v-else :classProp="'no-shadow no-border'" :deletable="false" :address="this.billingAddressForm"></address-card>
              </b-col>
              <b-col cols="3">
                <h5> Způsob doručení: </h5>
                <p> Slovenská pošta </p>
              </b-col>
              <b-col cols="3">
                <h5> Způsob platby </h5>
                <p> Dobírka</p>
              </b-col>
            </b-row>
            <hr>
          </div>

          <el-table v-if="step === 0 || step === 3"
            :data="items"
            show-summary
            :sum-text="'Celkem'"
            :summary-method="getSummaries"
            width="100%">
            <el-table-column
              label="Obrazek">
              <template slot-scope="scope">
                <div style="cursor: pointer;" @click="showProduct(scope.row.product)">
                  <product-image 
                    :product="scope.row.product"></product-image>
                </div>
              </template>
            </el-table-column>
            <el-table-column
              label="Nazev produktu">
              <template slot-scope="scope">
                  <span style="cursor: pointer;" @click="showProduct(scope.row.product)">
                    {{ getProductName(scope.row.product) }}
                  </span>
              </template>
            </el-table-column>
            <el-table-column
              prop="amount"
              label="Mnozstvi">
            </el-table-column>
            <el-table-column
              label="Cena/Ks">
              <template slot-scope="scope">
                {{ scope.row.product.price | price }}
              </template>
            </el-table-column>
            <el-table-column
              label="Cena bez DPH/Ks">
              <template slot-scope="scope">
                {{ (scope.row.product.price * ((100 - scope.row.product.tax_rate) / 100)) | price }}
              </template>
            </el-table-column>
            <el-table-column
              label="DPH celkem">
              <template slot-scope="scope">
                {{ (scope.row.product.price * ((100 - scope.row.product.tax_rate) / 100)) * scope.row.amount | price }}
              </template>
            </el-table-column>
            <el-table-column
              label="Cena celkem">
              <template slot-scope="scope">
                <b>{{ scope.row.product.price * scope.row.amount | price }}</b>
              </template>
            </el-table-column>
            <el-table-column
              label="Akce"
              width="150">
              <template slot-scope="scope">
                <el-button-group>
                  <el-button size="small" type="info" icon="el-icon-minus" @click="subtractAmount(scope.row)"></el-button>
                  <el-button size="small" type="info" icon="el-icon-plus" @click="addAmount(scope.row)"></el-button>
                  <el-button size="small" type="info" icon="el-icon-delete" @click="removeItem(scope.row)"></el-button>
                </el-button-group>
              </template>
            </el-table-column>
          </el-table>

          <div v-if="step === 1">
            <div v-if="isLoggedIn">
              <h5> Moje adresy </h5>
              <b-row>
                <b-col cols="4" v-for="address in userAddresses" :key="address.id" @click="useAddress(address)">
                  <address-card :deletable="false" classProp="box-card-white pointer" :address="address"></address-card>
                </b-col>
              </b-row>
              <hr>
            </div>

            <address-form ref="addressFormComponent" :address.sync="addressForm"></address-form>
            
            <el-checkbox v-model="isBillingDifferent">Fakturační se liší od doručovací</el-checkbox>
            <address-form 
              v-if="isBillingDifferent" 
              ref="billingAddressFormComponent" 
              :isBilling="true"
              :address.sync="billingAddressForm">
            </address-form>

          </div>

          <div v-if="step === 2">
            <b-row>
              <b-col cols="6">
                <h4> Způsob doručení </h4>
                <el-select style="width: 100%" v-model="selectedDeliveryOption" placeholder="Vyberte">
                  <el-option
                    v-for="item in deliveryOptions"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value">
                  </el-option>
                </el-select>
                <span v-if="!selectedDeliveryOption" style="color:red;">Povinné pole</span>
              </b-col>
              <b-col cols="6">
                <h4> Způsob platby </h4>
                <el-select style="width: 100%" v-model="selectedPaymentMethod" placeholder="Vyberte">
                  <el-option
                    v-for="item in paymentMethods"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value">
                  </el-option>
                </el-select>
                <span v-if="!selectedPaymentMethod" style="color:red;">Povinné pole</span>
              </b-col>
            </b-row>
          </div>

        </div>
        <hr>
        <h4 class="text-right"> Celková cena: {{ overallSum }} € </h4>
        <hr>
      </b-container>
      <span class="pull-right mt-3 mb-3 mr-3">
        <el-button v-if="step !== 0" type="primary" @click="moveStep(-1)">
          <i class="el-icon-d-arrow-left"></i>
          Vrátit se
        </el-button>
        <el-button v-if="step !== 3" type="primary" @click="moveStep(1)">
          Pokracovat
          <i class="el-icon-d-arrow-right"></i>
        </el-button>
        <el-button v-if="step === 3" type="success" @click="sendItOut">
          <i class="el-icon-success"></i>
          Dokončit objednávku
        </el-button>
      </span>

    </div>
  </div>
</template>

<script>
import { getFullProductName } from '@/helpers';
import ProductInCartMixin from '@/mixins/ProductInCartMixin';
import ProductImage from '@/components/product/ProductImage';
import AddressForm from '@/components/user/AddressForm';
import AddressCard from '@/components/user/AddressCard';

export default {
  mixins: [ProductInCartMixin],
  components: {
    'product-image': ProductImage,
    'address-form': AddressForm,
    'address-card': AddressCard,
  },
  data() {
    return ({
      step: 0,
      addressForm: {
        address: '',
        city: '',
        company: '',
        country: '',
        dic: '',
        first_name: '',
        last_name: '',
        telephone: '',
        zip: '',
      },
      billingAddressForm: {
        address: '',
        city: '',
        company: '',
        country: '',
        dic: '',
        first_name: '',
        last_name: '',
        telephone: '',
        zip: '',
      },
      selectedDeliveryOption: 1,
      selectedPaymentMethod: 1,
      isBillingDifferent: false,
      success: false,
      isLoading: false,
    });
  },
  mounted() {
    const storedAddress = this.$store.getters.getSubmitedAddress;
    const deliveryOption = this.$store.getters.getSubmitedDeliveryOption;
    const paymentMethod = this.$store.getters.getSubmitedPaymentMethod;

    this.success = false;

    if (storedAddress) {
      this.addressForm = storedAddress;
    }

    if (deliveryOption) {
      this.selectedDeliveryOption = deliveryOption;
    }

    if (paymentMethod) {
      this.selectedPaymentMethod = paymentMethod;
    }
  },
  computed: {
    items() {
      return this.$store.getters.getCartItems;
    },
    overallSum() {
      let sum = 0;
      this.items.forEach((item) => {
        sum += (item.product.price * item.amount);
      });

      return (sum / 100).toFixed(2);
    },
    isLoggedIn() {
      return this.$store.getters.isLoggedIn;
    },
    userAddresses() {
      return this.$store.getters.getAddresses;
    },
    deliveryOptions() {
      return [
        { label: 'Slovenská Pošta', value: 1 },
        { label: 'DPH', value: 2 },
      ];
    },
    paymentMethods() {
      return [
        { label: 'Dobírka', value: 1 },
      ];
    },
  },
  methods: {
    async sendItOut() {
      this.isLoading = true; 

      await this.$store.dispatch('SUBMIT_ORDER', {
        address: this.addressForm,
        deliveryOption: this.selectedDeliveryOption,
        paymentMethod: this.selectedPaymentMethod,
        billingAddress: this.isBillingDifferent ? this.billingAddressForm : false,
      });

      this.$store.commit('CLEAN_CART');

      this.isLoading = false;
      this.success = true;
    },
    getProductName(product) {
      return getFullProductName(product);
    },
    subtractAmount(item) {
      if (item.amount > 1) {
        this.handleAmountChange(item, item.amount - 1);
      }
    },
    addAmount(item) {
      this.handleAmountChange(item, item.amount + 1);
    },
    getSummaries() {
      const sums = [];
      let dphSum = 0;
      this.items.forEach((item) => {
        dphSum += (item.product.price * ((100 - item.product.tax_rate) / 100) * item.amount);
      });

      dphSum = (dphSum / 100).toFixed(2);

      sums[4] = 'CELKEM: ';
      sums[5] = `${dphSum} €`;
      sums[6] = `${this.overallSum} €`;
      return sums;
    },
    useAddress(address) {
      this.addressForm = Object.assign({}, address);
    },
    dispatchStepRelatedAction() {
      switch (this.step) {
        case 1:
          this.$store.commit('SET_ADDRESS', this.addressForm);
          this.$store.commit('SET_BILLING_ADDRESS', this.billingAddressForm);
          break;
        case 2:
          this.$store.commit('SET_DELIVERY_OPTION', this.selectedDeliveryOption);
          this.$store.commit('SET_PAYMENT_METHOD', this.selectedPaymentMethod);
          break;
        default:
          break;
      }
    },
    moveStep(amount) {
      if (amount > 0) {
        if (this.step < 3 && this.canStepForward()) {
          this.dispatchStepRelatedAction();
          this.step += 1;
          return;
        }
      }

      if (this.step > 0 && amount < 0) {
        this.dispatchStepRelatedAction();
        this.step -= 1;
      }
    },
    canStepForward() {
      if (this.step === 1) {
        if (!this.isBillingDifferent) {
          return this.$refs.addressFormComponent.isFormValid();
        }
        return (
          this.$refs.addressFormComponent.isFormValid() &&
           this.$refs.billingAddressFormComponent.isFormValid()
        );
      }

      if (this.step === 2) {
        return (this.selectedDeliveryOption && this.selectedPaymentMethod);
      }

      return true;
    },
  },
};
</script>

<style>
  .el-table__footer {
    font-size: 1.2em;
    font-weight: bold;
  }

</style>
