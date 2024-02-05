## Stripe app


## Requirement

- 2 Stripe account
 - 1: For uploading (and publish) Stripe App
 - 2: Install the Stripe app for testing
-  ** node version v20.0.0 **

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

![WordPress site]('/assets/images/step1.png')


### Step2: [/wordpress] Set HTTP proxy by `ngrok`

For connecting with Stripe App, we need to get a public URL. So use `ngrok` to get the proxy URL to the local WordPress site:
If the WordPress URL is `http://localhost:8881` you can run `ngrok` like this:

```bash
$ ngrok http 8881
```

`ngrok` will provide a public URL, so please copy the URL.

### Step3: [/stripe-app] Seting up Stripe App

Let's move on to configure Stripe App, open the `stripe-app/stripe-app.json` for putting the OAuth callback URL:

```diff
  "allowed_redirect_uris": [
-    "https://REPLACE_YOUR_WORDPRESS_URL/wp-json/stripe-apps/v1/callback"
+    "https://{YOUR_NGROK_URL}/wp-json/stripe-apps/v1/callback"
  ],
```

`{YOUR_NGROK_URL}` is a URL you get from the `ngrok http` command.

### Step4: [/stripe-app] Upload Stripe app for get the Stripe APP install URL

You need to upload this app into your Stripe account.

```bash
$ npm run -w stripe-app upload
```

After succeeded to upload the app, you need to run this command:

```bash
$ npm run -w stripe-app stripe apps set distribution public
```

By default, a Stripe App will always be uploaded as a private app due to the number limitation of public apps (1 app per account). Therefore, we need to run this command to make it clear that we want to use this app publicly.

After updating the distribution setting, please upload this app again.

```bash
$ npm run -w stripe-app upload
```

On the Stripe dashboard, you can get the Installation link on the "External test" tab:

![Stripe App setting]('/assets/images/step2.png')

Please copy the URL on the `Test mode link`.

### Step5: [/wordpress] Put the API Key and Stripe App install URL

Move to the WordPress site, please visit the administration page through the menu in the header.

![Stripe App setting]('/assets/images/step2.png')

Click the `Stripe App` tab in the sidebar menu.

![Stripe App setting]('/assets/images/step3.png')

Please put the `Test mode link` starting from 'https://marketplace.stripe.com/oauth' into the `App Install URL` field.
And put your Stripe Secret API key into the `Secret API key` field.

![Stripe App setting]('/assets/images/step4.png')

Press the `Save settings` button to save these data.

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