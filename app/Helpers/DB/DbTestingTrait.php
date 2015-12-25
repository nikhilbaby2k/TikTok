<?php namespace App\Helpers\DB;

use DB;

trait DbTestingTrait {

    /*ALL FUNCTIONS BELOW ARE PURELY FOR TESTING PURPOSE AND HAVE PURPOSELY BEEN SUFFIXED WITH A 't' AT THE END SO THAT
    THERE IS NO CONFLICT WITH OTHER FUNCTIONS THAT MAY BE CALLED.*/
    /**
     * This function is purely done for testing, refer QueryBuilderTest, ...(add any other tests using this here)
     * @return $this
     */
    public function userst()
    {
        return $users = DB::table("users")->select(['user_id', 'first_name', 'last_name', 'email','registration_date']);
    }

    /**
     * This function is purely done for testing first letter Caps, refer QueryBuilderTest,...(add any other tests using this here)
     * @return $this
     */
    public function Pagest()
    {
        return $users = DB::table("campaign_pages")->select(['*']);
    }

    /**
     * This function is purely done for testing camelCase two words, refer QueryBuilderTest, ...(add any other tests using this here)
     * @return $this
     */
    public function websiteDetailst()
    {
        return $users = DB::table("website_details")->select(['*']);
    }

    /**
     * This function is purely done for testing Pascal Case two words, refer QueryBuilderTest, ...(add any other tests using this here)
     * @return $this
     */
    public function CampaignPagest()
    {
        return $users = DB::table("website_details")->select(['*']);
    }

    /**
     * This function is purely done for testing functions with underscore (will fail), refer QueryBuilderTest, ...(add any other tests using this here)
     * @return $this
     */
    public function user_detailst()
    {
        return $users = DB::table("users")->select(['*']);
    }

    public function userIdt($user_id){


        return $users = DB::table("users")->select(['*'])->where('user_id',$user_id);
    }

    public function userByNamet($first_name,$last_name){


        return $users = DB::table("users")->select(['*'])->where('first_name','LIKE','%'.$first_name.'%')->where('last_name','LIKE','%'.$last_name.'%');
    }
}