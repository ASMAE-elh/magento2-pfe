# OneStepCheckout
Custom checkout for Guest user - Tested on Magento 2.4

## Overview

The OneStepCheckout module customizes the Magento 2 checkout process to streamline and simplify the user experience. It offers features such as setting default shipping values, hiding unnecessary fields, and displaying custom messages based on the subtotal. The module also provides configuration options in the Magento admin to enable or disable the module for specific stores. After installing this module, guest users can place an order quickly and efficiently within 1 minute. Customers need to enter their data in fields such as Email, Full Name, Address, and Phone.

## Features

- Sets default country and values for shipping address fields.
- Hides specific fields in the checkout process.
- Displays custom messages based on the subtotal amount.
- Allows enabling or disabling the module for specific stores through admin configuration.

## Installation

1. **Download the module:**

   Clone or download the module from the GitHub repository.

```sh
   git clone https://github.com/your-repo/wb-onestepcheckout.git
```

2. **Copy the module to your Magento directory:**

Copy the module files to the app/code/WB/OneStepCheckout directory.

2. **Enable the module:**

Run the following commands in your Magento root directory:

```
php bin/magento module:enable WB_OneStepCheckout
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
php bin/magento cache:clean
php bin/magento cache:flush
```

## Configuration

1. **Log in to the Magento admin panel.**
2. **Navigate to Stores > Configuration > Sales > Checkout > WB One Step Checkout.**
3. **Enable or disable the module for specific stores and configure the custom note to display on the checkout page.**

## Usage

When the module is enabled and configured, it will:

1. Set default values for shipping address fields.
2. Hide specific fields in the checkout process.
3. Display custom messages based on the subtotal in the order summary section.

## Support

For any issues or feature requests, please open an issue on the GitHub repository.