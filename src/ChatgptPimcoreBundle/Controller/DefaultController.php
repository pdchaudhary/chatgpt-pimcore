<?php

namespace Pdchaudhary\ChatgptPimcoreBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Workflow\Registry;
use Pimcore\Model\Document;
use Pimcore\Workflow\Manager;
use Pimcore\Db;
use Pimcore\Logger;
use Pimcore\Tool;
use Pimcore\Tool\Authentication;
use Symfony\Component\HttpFoundation\JsonResponse;
use Pimcore\Model\Document\Snippet;
use Pimcore\Model\DataObject;
use Pimcore\Model\WebsiteSetting;
use Pimcore\Model\DataObject\ClassDefinition\Data\Localizedfields;
use OpenAI;
use Pimcore\Model\DataObject\ClassDefinition\Data;


class DefaultController extends FrontendController
{

   
   
 /** 
     * @Route("/admin/chatgpt/object-fields")
    */
    public function getFields(Request $request)
    {
        $objectId = $request->get('objectId');
        $object = DataObject::getById((int)$objectId);
        
        $fieldItems = [];
        if($object){
            // Get the class definition
            $class = $object->getClass();

            // Get the field definitions
            $fields = $class->getFieldDefinitions();

            foreach ($fields as $field) {
                if (
                    $field instanceof Data\Textarea
                    || $field instanceof Data\Wysiwyg
               
                ) {
                    $fieldItems[] =
                    [ 
                        'id' =>$field->getName(),
                        "name" => $field->getTitle(),
                        'is_localizedfield'=>false,
                    ];
                }

                if (
                    $field instanceof Localizedfields

                )
                {
                    $localizedfields = $field->getFieldDefinitions();
                    foreach ($localizedfields as $localizedfield) {
                        if (
                             $localizedfield instanceof Data\Wysiwyg
                            || $localizedfield instanceof Data\Textarea
                       
                        ) {
                            $fieldItems[] =
                            [ 
                                'id' =>$localizedfield->getName(),
                                "name" => $localizedfield->getTitle(),
                                'is_localizedfield'=>true,
                            ];
                        }
                    }
                }

            }
        }
        return new JsonResponse([
            "data" =>  $fieldItems
        ]);

    }


    /** 
     * @Route("/admin/map-description-field")
     * 
    */

    public function mapDescriptionField(Request $request){
        $objectId = $request->get('objectId');
        $field = $request->get('field');
        $language = $request->get('language');
        $object = DataObject::getById((int)$objectId);
    

        if($object){
            // Get the class definition
            $class = $object->getClass();

            $description = "Write ".$class->getName()." ".$field." for following information";

            if($language){
                $description .=" in ".$language;
            }
            $description .=":-\n";
            // Get the field definitions
            $fields = $class->getFieldDefinitions();

            foreach ($fields as $field) {
                if (
                    $field instanceof Data\Input
                    || $field instanceof Data\Select
                    || $field instanceof Data\Date
                    || $field instanceof Data\Numeric
                    || $field instanceof Data\DateTime
                    || $field instanceof Data\Multiselect
                  

                    
               
                ) {
                    $value = $object->{'get'.ucwords($field->getName())}();
                    if($value){
                        if(is_array($value) || is_object($value)){
                            $value = json_encode($value);
                        }
                        $description .= $field->getTitle().':'.$value."\n";
                    }
                    
                }

                if (
                    $field instanceof Localizedfields

                )
                {
                    $localizedfields = $field->getFieldDefinitions();
                    foreach ($localizedfields as $localizedfield) {
                        if (
                             $localizedfield instanceof Data\Input
                            || $localizedfield instanceof Data\Select
                            || $localizedfield instanceof Data\Multiselect
                            || $localizedfield instanceof Data\Date
                            || $localizedfield instanceof Data\DateTime
                            || $localizedfield instanceof Data\Numeric
                           
                       
                        ) {
                            $value = $object->{'get'.ucwords($localizedfield->getName())}();
                            if($value){
                                $description .= $localizedfield->getTitle().':'.$value."\n";
                            }
                            
                            
                        }
                    }
                }

            }
        }

        return new JsonResponse([
            "data" =>  $description
        ]);


    }

     /** 
     * @Route("/admin/chatgpt/generate-description")
    */
    public function generateDescription(Request $request)
    {
        $objectId = $request->get('objectId');
        $description = $request->get('description');
        $field = $request->get('field');
        $lang = $request->get('lang');
        $max_tokens = $request->get('max_tokens');
        $object = DataObject::getById((int)$objectId);
        if (!$object->isAllowed('view')) {
            return new JsonResponse([
                "success" =>  false,
                "message" => "Access denied"
            ]);
        }
        $apiKey = $this->getChatGPTAuthKey();

        if(empty( $apiKey)){
            return new JsonResponse([
                "success" =>  false,
                "message" => "The instruction states to generate an API key from OpenAI and add the generated key, labeled as 'chatgpt_auth_key', in the website settings. This key is likely used for authentication purposes to interact with OpenAI's ChatGPT API. By following this instruction, you can obtain the necessary credentials to authenticate and access the API, enabling communication with the ChatGPT model on your website or application."
            ]);
        }
        $client =  OpenAI::client($apiKey);
        $model = $this->getChatGPTModal();

        if(empty( $model)){
            return new JsonResponse([
                "success" =>  false,
                "message" => "labeled as 'chatgpt_model', in the website settings. This key is likely used for model purposes to interact with OpenAI's ChatGPT API."
            ]);
        }
        $result = $client->completions()->create([
            'model' => $model,
            'prompt' => $description,
            'max_tokens' => (int)$max_tokens,
            'temperature' => 0.0
        ]);
        $text = '';
        $response = $result->toArray();
     
        if(isset($response['choices']) && !empty($response['choices'])){
            $text = $response['choices'][0]['text'];
        }
       

        
        if($text){
            if($lang){
                $object->{'set'.ucwords($field)}($text,$lang);
            }else{
                $object->{'set'.ucwords($field)}($text);
            }
            $object->save();
            return new JsonResponse([
                "success" =>  true,
                "message" => "The field data was successfully updated using the ChatGPT."
            ]);
        }else{
            return new JsonResponse([
                "success" =>  false,
                "message" => "The AI did not provide any data or information based on description."
            ]);
        }
       

        
    }


    public function getChatGPTAuthKey(){
        $authKey = WebsiteSetting::getByName("chatgpt_auth_key") ? WebsiteSetting::getByName("chatgpt_auth_key")->getData() : null;
        return $authKey;
    }

    public function getChatGPTModal(){

        $chatgptModel = WebsiteSetting::getByName("chatgpt_model") ? WebsiteSetting::getByName("chatgpt_model")->getData() : null;
        return $chatgptModel;
       
    }
    
}

