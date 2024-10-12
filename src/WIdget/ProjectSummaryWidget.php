<?php
namespace App\Widget;

use App\Entity\Configuration;
use App\Entity\Project;
use App\Widget\WidgetInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ProjectSummaryWidget implements WidgetInterface {


    public function __construct(private $em, private $security) {
    }


    public function getName(): string {
        return 'Project Review';
    }

    public function getWidth(): int {
        return 12;
    }

    public function getHeight(): int {
        return 3;
    }

    public function render(): string {
        //TODO: Implement Voters and deny access to unauthorized users
        $projects = [];
        if($this->security->getUser()->getCompany()!==null){
            $projects = $this->em->getRepository(Project::class)->retrieveCommentablesFromCompany($this->security->getUser()->getCompany());
        }
        if(count($this->security->getUser()->getCommentableMembers()) > 0){
            $projects = array_merge($projects,$this->security->getUser()->getCommentableMembers()->toArray());
        }
        $output =  '<div class="card"><div class="card-body">
            <h2>Project Review</h2>
            <table>
                <tr>
                    <th>Project</th>
                    <th>Tasks</th>
                </tr>
                <tr>
                ';
        foreach($projects as $project){
            $output .= '<td>'.$project->getName().'</td>';
            $output .= '<td>'.count($project->getTasks()).'</td>';
        }
        $output .='
                </tr>
            </table>
        </div></div>';
        return $output;
    }

    public function getContext(): array {
        return [];
    }
    public function isForThisUserAvailable(): bool {
        $user = $this->security->getUser();
        if(count($user->getCommentableMembers()) > 0)
            return true;
        if($user->getCompany()!==null){
           $hasProjectRight =  $this->em->getRepository(Configuration::class)->findOneBy(['name' => 'hasProject', 'company' => $user->getCompany()]);
           if( $hasProjectRight->getValue() == 1)
                return true;
        }
            
        return false;
    }
}