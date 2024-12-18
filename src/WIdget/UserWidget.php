<?php
namespace App\Widget;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Environment as Twig;

class UserWidget implements WidgetInterface
{
	public function getName(): string
	{
		return 'User Review';
	}
	public function getWidth(): int
	{
		return 12;
	}
	public function getHeight(): int
	{
		return 3;
	}
	public function render(): string
	{
		$output =
			'<div class="card"><div class="card-body">
			<div class="col-md-12">
				<h2>
					Benutzer
				</h2>
				<table class="table datagrid">
					<thead>
						<tr>
							<th>Name</th>
							<th></th>
							<th>Role</th>
							<th>Group</th>
							<th>Overtime</th>
							<th>Statut</th>
							<th>Holiday</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>';
		foreach ($this->security->getUser()->getCompany()->getUsers() as $user) {
			$output .= '
						<tr>
							<td>
								' . $user->getName() . '
							</td>
                            <td></td><td>
							' . $user->getRoles()[0] . '	
							</td>
							<td>
							'
				. ($user->getWorkingGroup() !== null ? $user->getWorkingGroup()->getName() : '') . '
							</td>
							<td>
							</td>							
							<td>
							' . $user->getState() . '
							</td>
							<td>
							</td>							
							<td>
							</td>							
						</tr>
						';
		}
		$output .= '
					</tbody>
				</table>
			</div>

                </div></div>';
		return $output;
	}
	public function getContext(): array
	{

	}

	public function isForThisUserAvailable(): bool
	{
		return true;
	}

	public function __construct(private EntityManagerInterface $em, private Security $security, private Twig $twig)
	{
	}

}
