<?php
namespace AppBundle\Controller;

use AppBundle\Wechat\Wechat;
use Imagine\Gd\Imagine;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Helper;
use AppBundle\Entity;
use Symfony\Component\Validator\Constraints\DateTime;

#use Symfony\Component\Validator\Constraints\Image;

class DefaultController extends Controller
{
	/**
	 * @Route("/", name="_index")
	 */
	public function indexAction()
	{
		return $this->render('AppBundle:default:index.html.twig');
	}
	/**
	 * @Route("/game/{t}", name="_game")
	 */
	public function gameAction(Request $request, $t = 'a')
	{
		$t = strtolower($t);
		switch ($t) {
			case 'a':
				$officer = '金完美';
				break;
			case 'b':
				$officer = '宁排长';
				break;
			default:
				$officer = '暖男杨';
				$t = 'c';
				break;
		}
		$session = $request->getSession();
		$session->set('wx_desc', '我正在接受'.$officer.'教官的超规格军训！');
		return $this->render('AppBundle:default:game/'.$t.'.html.twig');
	}
	/**
	 * @Route("/share/{t}", name="_share")
	 */
	public function shareAction(Request $request, $t = 'a')
	{
		$t = strtolower($t);
		switch ($t) {
			case 'a':
				$officer = '金完美';
				break;
			case 'b':
				$officer = '宁排长';
				break;
			default:
				$officer = '暖男杨';
				break;
		}
		$session = $request->getSession();
		$session->set('officer', $officer);
		$session->set('wx_desc', '我正在接受'.$officer.'教官的超规格军训！');
		return $this->render('AppBundle:default:share.html.twig');
	}
	/**
	 * @Route("/info", name="_info")
	 */
	public function infoAction(Request $request)
	{
		$session = $request->getSession();
		$officer = $session->get('officer');
		$session->set('wx_desc', '我正在接受'.$officer.'教官的超规格军训！');
		return $this->render('AppBundle:default:info.html.twig');
	}
	/**
	 * @Route("/finish", name="_finish")
	 */
	public function finishAction()
	{
		$session = $request->getSession();
		$officer = $session->get('officer');
		$session->set('wx_desc', '我正在接受'.$officer.'教官的超规格军训！');
		return $this->render('AppBundle:default:finish.html.twig');
	}
	/**
	 * @Route("/post", name="_post")
	 */
	public function postAction(Request $request)
	{
		$return = array(
			'ret' => 0,
			'msg' => '',
		);
		$session = $request->getSession();
		if( $request->getMethod() == "POST"){
			$em = $this->getDoctrine()->getEntityManager();
			$repo = $em->getRepository('AppBundle:Info');
	            $qb = $repo->createQueryBuilder('a');
	            $qb->select('COUNT(a)');
	            $qb->where('a.mobile = :mobile');
	            $qb->setParameter('mobile', $request->get('mobile'));
	            $count = $qb->getQuery()->getSingleScalarResult();
	            if($count > 0){
	            	$return['ret'] = 1004;
				$return['msg'] = '该手机号已经提交过信息啦';
	            }
			elseif( null == $request->get('username')){
				$return['ret'] = 1001;
				$return['msg'] = '用户名不能为空';
			}
			elseif( null == $request->get('address')){
				$return['ret'] = 1002;
				$return['msg'] = '用户名不能为空';
			}
			elseif( null == $request->get('mobile')){
				$return['ret'] = 1003;
				$return['msg'] = '手机不能为空';
			}
			else{
				$info = new Entity\Info;
				$info->setUsername($request->get('username'));
				$info->setAddress($request->get('address'));
				$info->setMobile($request->get('mobile'));
	            	$info->setCreateIp($request->getClientIp());
	            	$info->setCreateTime(new \DateTime('now'));
				$em->persist($info);
				$em->flush();
				$officer = $session->get('officer');
				$return['wx_desc'] = $request->get('username').'正在接受'.$officer.'教官的超规格军训！';
			}
		}
		else{
			$return['ret'] = 1004;
			$return['msg'] = '来源不正确~';
		}
		return new Response(json_encode($return));
	}
}
