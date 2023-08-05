# chatgpt-pimcore
Enhance product data quality and streamline content creation with the Pimcore and ChatGPT integration.

# Overview

The integration of Pimcore and ChatGPT offers a powerful solution to enhance product data quality and streamline content creation processes. By leveraging this integration, businesses can optimize their data management workflows and improve the accuracy and consistency of product information. ChatGPT, a sophisticated language model, utilizes attribute-based inputs to generate detailed descriptions automatically. This integration empowers organizations to efficiently create high-quality content, resulting in enhanced customer experiences and improved productivity.


* Pimcore 10.6

```bash
composer require pdchaudhary/chatgpt-pimcore:1.4
``` 
* Pimcore 11

```bash
composer require pdchaudhary/chatgpt-pimcore
``` 

# Installation

1. Open the Pimcore admin panel.
2. Navigate to Settings and click on Bundles.
3. Click on the plus icon (+) to enable the plugin.
4. Proceed to add the chatgpt_auth_key in the website settings (Settings -> Website settings).
5. Generate an API key from ChatGPT with read and write access. For instructions on generating a ChatGPT API key, please refer to the ChatGPT API documentation.




Here is a step-by-step description of the process after installing the integration:

1. Once the integration is installed, you will notice a new ChatGPT button added on the object toolbar.
2. Clicking on the ChatGPT button will open a popup window.
3. Inside the popup, you will find three main fields-
   a. Selection of Field: Choose the field for which you want to generate AI-based content. This could be some specific textarea or wysiwyg type field within the object.
   b. AI Query Field: Use this field to customize the query that is being sent to the ChatGPT model.
   c. Number of Words: Specify the desired length of the generated content in terms of the number of words.
4. Fill in the necessary information in the fields according to your requirements.
5. Submit the query or input to the ChatGPT model by clicking a button or pressing enter.
6. The ChatGPT integration will process the request and generate AI-based content based on the provided field selection, query, and word limit.
7. The generated content will be automatically saved on selected field.

![image](https://github.com/pdchaudhary/chatgpt-pimcore/assets/30948231/32388a9c-e588-4e22-8433-da9d1d252c9b)

