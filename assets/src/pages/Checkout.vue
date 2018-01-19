<template>
  <div>
    <b-container style="margin-bottom: 75px;">
      <h1 class="mt-3"> Dokončení objednávky </h1>

      <div style="padding: 10px;">
        <el-steps class="mt-3" :active="step" finish-status="success" align-center>
          <el-step title="Shrnutí objednávky" icon="el-icon-info"></el-step>
          <el-step title="Zákazník" icon="el-icon-location"></el-step>
          <el-step title="Způsob platby / Dopravy" icon="el-icon-menu"></el-step>
          <el-step title="Hotovo!" icon="el-icon-star-on"></el-step>
        </el-steps>

        <hr>
        <h4 class="text-right"> Celkem: {{ overallSum }} € </h4>
        <hr>

        <el-table v-if="step === 0"
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
            width="200">
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
            <address-card classProp="box-card-white" v-for="address in userAddresses" :key="address.id" :address="address"></address-card>
            <hr>
          </div>

          <address-form ref="addressFormComponent" :address.sync="addressForm"></address-form>
        </div>

      </div>
    </b-container>
    <div style="position: fixed; bottom: 0px; left: 0px; width: 100%; height: 75px;">
      <span class="pull-right mt-3 mb-3 mr-3" style="background-color: black; padding: 10px; opacity: 0.8">
        <el-button :disabled="step === 0" type="primary" @click="moveStep(-1)">
          <i class="el-icon-d-arrow-left"></i>
          Vrátit se
        </el-button>
        <el-button :disabled="step === 3" type="primary" @click="moveStep(1)">
          Pokracovat
          <i class="el-icon-d-arrow-right"></i>
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
      step: 1,
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
    });
  },
  computed: {
    items() {
      return this.$store.getters.getCartItems;
    },
    overallSum() {
      let sum = 0;
      this.items.forEach((item) => {
        sum += (item.product.price * item.amount)
      });

      return (sum / 100).toFixed(2);
    },
    isLoggedIn() {
      return this.$store.getters.isLoggedIn;
    },
    userAddresses() {
      return this.$store.getters.getAddresses;
    }
  },
  methods: {
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
    getSummaries(props) {
      console.log(props);
      let sums = [];
      
      let dphSum = 0;
      this.items.forEach((item) => {
        dphSum += (item.product.price * ((100 - item.product.tax_rate) / 100) * item.amount);
      });

      dphSum = (dphSum / 100).toFixed(2);

      sums[4] = 'CELKEM: '
      sums[5] = dphSum + ' €';
      sums[6] = this.overallSum + ' €';
      return sums;
    },
    moveStep(amount) {
      if (amount > 0) {
        if (this.step < 3 && this.canStepForward()) {
          this.step++;
        }
      } else {
        if (this.step > 0) {
          this.step--;
        }
      }
    },
    canStepForward() {
      if (this.step === 1) {
        return this.$refs.addressFormComponent.isFormValid();
      }

      return true;
    }
  },
};
</script>

<style>
  .el-table__footer {
    font-size: 1.2em;
    font-weight: bold;
  }

</style>
