# Dokan Geolocation Calculator

Admin sets coordinates for each store and plugin find nearest store from user's location in a API request.

## Description

This wordpress plugin adds menu beside dokan-menu and admin can set latitude, longitude, acceptable diameter for each store.
Also, set new WP-api endpoint that accept lat & lng args as user location and response only nearest store name or message.

## Installation

1. Upload `zip-file` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. go to `Dokan Stores PDB` menu and set latitude, longitude, diameter, enabled options
4. enable WP-API and set message for no store found condition

### Prerequisites:
- installed Dokan-lite & Dokan-pro.
- WP-API enabled.

## Frequently Asked Questions

###### Why Dokan-pro must be installed?

every vendor/store must have `City` field that Dokan-pro provide this option.

## Screenshots

![Screenshot-API](https://github.com/pooryadb/dokan_geolocation_calculator/blob/master/assets/Screenshot-API.jpg)

![Screenshot-Coordinates](https://github.com/pooryadb/dokan_geolocation_calculator/blob/master/assets/Screenshot-Coordinates.gif)

### API Output

##### success:
_request_
```http request
POST http://{WEBSITE URL}/wp-json/dgcpdb/v1/find_store
BODY [form-data]
 lat : 31.138181
 lng : 48.606906 
```
_response_
```json
{
    "message": "",
    "city": "Tehran"
}
```
##### fail:
_request_
```http request
POST http://{WEBSITE URL}/wp-json/dgcpdb/v1/find_store
BODY [form-data]
 lat : 32.138181
 lng : 48.606906 
```
_response_
```json
{
    "message": "هیچ فروشگاهی در نزدیکی شما پیدا نشد! سوالی دارید؟ <a href=\"http://romroid.ir\">اینجا کلیک کنید</a>",
    "city": null
}
```

## Changelog

## Upgrade Notice

------------------------------------------------------------------
**Contributors:** @pooryaDb [website](http://romroid.ir) / [telegram](https://t.me/pooryadb)

**Donate link:** 

*USDT (TRC20) :* `TKpWx9sukxDqfRhWzXTKmUbYSm37BeeC4F`

*USDT (ERC20) :* `0x8a3e8bf35f52b391727782f6c40e463ffad23327`

*ETH (BSC) :* `0x8a3e8bf35f52b391727782f6c40e463ffad23327`

*ETH (ERC20) :* `0x8a3e8bf35f52b391727782f6c40e463ffad23327`

**Tags:** wordpress, dokan, plugin, geolocation, rest-api, api, php, woocommerce, multivendor, multi-vendor, shop, vendor, seller, store, sell, online,multi seller, multi store, multi vendor, multi vendors, multistore, multivendor, product vendor, product vendors, vendor, vendor system, vendors, wc market place, wc marketplace, wc vendors, woo vendors, woocommerce market place, woocommerce marketplace, woocommerce multi vendor, e-commerce

**Requires at least:** 3.0.1

**Tested up to:** 5.4.4

**Stable tag:** 5.4.4

**License:** GPLv2 or later

**License URI:** http://www.gnu.org/licenses/gpl-2.0.html
