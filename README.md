## Stripe app


## Requirement

- 2 Stripe account
 - 1: For uploading (and publish) Stripe App
 - 2: Install the Stripe app for testing
-  Node.js (LTS)

## Getting started

You can download two application ( Stripe App, Demo WordPress Site ) from the following Git command:

```bash
% git clone git@github.com:hideokamoto-stripe/stripe-app-plugin-for-wordpress.git
% npm install
```

### Step1: [/wordpress] Setting up WordPress

You need to configure the WordPress plugin. Please run the `npm run -w wordpress start` to launch the WordPress.

```bash
$ npm run -w wordpress start

> wordpress@1.0.0 start
> wp-now start

...

```

The command will show the URL for the WordPress site:

```bash
Starting the server......
directory: /Users/stripe/samples/stripe-app-example-wordperss-oauth/wordpress
mode: plugin
php: 8.0
wp: latest
WordPress latest folder already exists. Skipping download.
SQLite folder already exists. Skipping download.
Server running at http://localhost:8881
```

Visit the WordPress site from the URL from the CLI result.

![assets/images/step1.png]('WordPress site')


### Step2: [/wordpress] Set HTTP proxy by `ngrok`
### Step3: [/stripe-app] Seting up Stripe App
### Step4: [/stripe-app] Upload Stripe app for get the Stripe APP install URL
### Step5: [/wordpress] Put the API Key and Stripe App install URL
### Step6: [/wordpress] Save the data and start to OAuth flow
### Step7: Create a new Stripe customer
### Step8: [/wordpress] Fetch customer data

### Setup

```bash
$ npm install -w stripe-app
```

### Start application

```bash
$ npm run -w stripe-app start
```

### Useful command

#### Stripe CLI

```bash
$ npm run -w stripe-app stripe <SUB_COMMAND>

# example
$ npm run -w stripe-app stripe apps grant permission "customer_read" "List the customer names"
```

#### Lint

```bash
$ npm run -w stripe-app lint
```

#### Unit test
```bash
$ npm run -w stripe-app test
```

## WordPress

### Setup

```bash
$ npm install -w wordpress
```

### Start application

```bash
$ npm run -w wordpress start
```