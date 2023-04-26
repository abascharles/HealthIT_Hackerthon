<?php
namespace App\Http\ViewComposers;

use Illuminate\View\View;

use App\DashboardCacher;

/**
* 
*/
class DashboardComposer
{
	
	/**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('widgets', DashboardCacher::dashboard());
    }
	

	/**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function tasks(View $view)
    {
        $view->with('tasks', DashboardCacher::tasks());
    }
	

	/**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function users(View $view)
    {
        $view->with('user', session('logged_facility'));
    }	

}