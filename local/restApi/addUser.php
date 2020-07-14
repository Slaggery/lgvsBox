<?php

class User
{
    const IDBLOCK = 17;

    public static function whatDoUser($query)
    {

        $users = json_decode($query['mData'], true);

        Log::logFile('Количество пользователей: ', count($users), 'addUser.log');

        $result = [];
        foreach ($users as $user) {
            $el = APILists::getElement(self::IDBLOCK, $user['Guid1C'], ['ID', 'NAME']);

            if (count($el) == 0) {
                $result[] = APILists::addElement(self::IDBLOCK, $user);
                Log::logFile('Пользователь добавлен в список: ', current($result), 'addUser.log');
            } else {
                $idElement = current($el);
                $result[] = APILists::updateElement($idElement['ID'], $user);
                Log::logFile('Пользователь обновлен в списке: ', current($result), 'addUser.log');
            }
        }

        return $result;
    }
}