export default {
  error: {
    api: 'Nastala chyba spojenia ({status}).',
  },
  sidebar: {
    orders: {
      title: 'História objednávok',
      titleDetail: 'Detail objednávky',
      noLogin: 'Pre históriu je potrebné sa prihlásiť',
      buttons: {
        cancel: 'Zrušiť',
        showMore: 'Zobraziť viac',
      },
      dialog: {
        cancel: 'Vážne chcete stornovat túto objednávku?',
      },
    },
    user: {
      title: 'Užívateľ',
      buttons: {
        login: 'Prihlásiť',
        logout: 'Odhlásiť',
        saveChanges: 'Uložiť zmeny',
        registration: 'Registrovať',
      },
      form: {
        name: 'Meno',
        surname: 'Priezvisko',
        phone: 'Tel. číslo',
        address: 'Adresa',
        city: 'Město',
        zip: 'PSČ',
        country: 'Země',
        emailRequired: 'Pole e-mail je povinné',
        emailTypeEmail: 'Je potrebné vložiť validný email',
        passwordRequired: 'Pole heslo je povinné',
        passwordMin: 'Heslo musí mať aspoň 4 znaky',
        phoneRequired: 'Je potřeba vyplnit telefon',
        countryCode: 'Je potřeba zadat předvolbu',
        postalCodeRequired: 'Je potřeba zadat PSČ',
        correctPostalCode: 'PSČ musí být ve správném tvaru',
      },
      misc: {
        or: 'alebo',
      },
    },
    cart: {
      title: 'Košík',
      misc: {
        together: 'Spolu',
      },
      buttons: {
        order: 'Objednať',
      },
    },
  },
  pages: {
    detail: {
      buttons: {
        addToCart: 'Pridať do košíka',
      },
      misc: {
        inBasket: 'V košíku',
        name: 'Názov',
        value: 'Hodnota',
      },
    },
    main: {
      misc: {
        bestSellers: 'Najpredávanejšie produkty',
        newsetProducts: 'Najnovšie produkty',
      },
    },
  },
  other: {
    withDPH: 's DPH',
    withoutDPH: 'bez DPH',
    piece: 'ks',
    yes: 'Ano',
    no: 'Nie',
    cancel: 'Zrušiť',
    confirm: 'Potvrdiť',
    save: 'Uložiť',
    fieldRequired: 'Je potrebné vyplniť',
  },
  messages: {
    userSignedIn: 'Uživateľ prihlásený',
    deleteAddressConfirm: 'Vážně si přejete vymazat adresu?',
    deleteCompleted: 'Adresa byla smazána',
    deleteCanceled: 'Smazání bylo přerušeno',
  },
};
