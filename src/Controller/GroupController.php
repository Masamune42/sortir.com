<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use App\Form\GroupMemberType;
use App\Form\GroupType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/group", name="group_")
 */
class GroupController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function home(EntityManagerInterface $entityManager)
    {
        $groupRepository = $entityManager->getRepository(Group::class);
        $groupsCreated = $groupRepository->findBy(['creator' => $this->getUser()]);
        $groupsParticipant = $groupRepository->findByParticipantButNotCreator($this->getUser());

        return $this->render(
            'group/home.html.twig',
            compact(
                'groupsCreated',
                'groupsParticipant'
            )
        );
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager)
    {

        $group = new Group();
        $groupForm = $this->createForm(GroupType::class, $group);

        $groupForm->handleRequest($request);

        if ($groupForm->isSubmitted() && $groupForm->isValid()) {
            $group->setCreator($this->getUser());
            $group->addParticipant($this->getUser());
            $entityManager->persist($group);
            $entityManager->flush();

            $this->addFlash('success', 'Groupe privé créé avec succès, vous pouvez maintenant inviter des membres.');

            return $this->redirectToRoute('group_detail', ['id' => $group->getId()]);

        }


        return $this->render(
            'group/create.html.twig',
            [
                'groupFormView' => $groupForm->createView(),
            ]
        );
    }

    /**
     * @Route("/detail/{id}", name="detail", requirements={"id : \d+"})
     */
    public function detail(Group $group, Request $request, EntityManagerInterface $entityManager)
    {
        $addMemberForm = $this->createForm(GroupMemberType::class);
        $addMemberForm->handleRequest($request);

        if ($addMemberForm->isSubmitted() && $addMemberForm->isValid() && $this->getUser() == $group->getCreator()) {

            $userRepository = $entityManager->getRepository(User::class);
            $newMemberUsernameOrMail = $addMemberForm->getData()['usernameOrMail'];
            $newMember = $userRepository->findByUsernameOrMail($newMemberUsernameOrMail, $newMemberUsernameOrMail);

            if (count($newMember) > 0) {
                $newMember = $newMember[0];
                if (!in_array($newMember, $group->getParticipants()->toArray())) {
                    $group->addParticipant($newMember);
                    $entityManager->persist($group);
                    $entityManager->flush();
                    $this->addFlash('success', $newMember->getUserName().' ajouté(e) au groupe '.$group->getName());
                } else {
                    $this->addFlash('warning', $newMember->getUserName().' fait déjà partie du groupe');
                }
            } else {
                $this->addFlash('warning', 'Utilisateur introuvable.');
            }
        }

        return $this->render(
            'group/detail.html.twig',
            [
                'group' => $group,
                'addMemberFormView' => $addMemberForm->createView(),
            ]
        );
    }
}
