<?php


namespace App\Controllers;

class Users extends BaseController
{
    private $userModel;
    private $bodyData;

    //TODO _remap() codeigniter에서 지원해주는 메소드
    //TODO routes에서 calss 호출시 가장먼저 호출
    public function _remap(...$args)
    {
        //TODO 배열의 맨 앞의 값을 제거
        $method = array_shift($args);
        $this->userModel = new \App\Models\Users();
        $this->bodyData = json_decode(file_get_contents('php://input'), true);
        return $this->$method($args);
    }

    //TODO get 확인(페이지가 생성이 되었는지 먼저 확인한다.)
    public function index()
    {
        return view('welcome_message');
    }

    //TODO post 확인(postman으로 post로 값이 잘 전달되는지 확인)
    public function create()
    {

        //TODO 쿼리 빌더로 데이터의 값을 insert
        $insert_result = $this->userModel->insert($this->bodyData);
        $result = array();

        if ($insert_result == true) :
            $result['rst'] = 'ok';
            $result['rst_msg'] = '회원가입 성공';
        else:
            $result['rst'] = 'fail';
            $result['rst_msg'] = '회원가입 실패';
        endif;

        $result['id'] = $insert_result;
        return json_encode($result);
    }

    public function delete($id)
    {
        $result = array();

        //TODO email, password 일치하는 회원 찾기
        $select_result = $this->userModel
            ->where('email', $this->bodyData['email'])
            ->where('password', $this->bodyData['password'])
            ->findAll();
        $cnt_f = count($select_result);

        //TODO 삭제
        $delete_result = $this->userModel
            ->where('email', $this->bodyData['email'])
            ->where('password', $this->bodyData['password'])
            ->delete();

        //TODO 삭제한걸 확인
        $select_result = $this->userModel
            ->where('email', $this->bodyData['email'])
            ->where('password', $this->bodyData['password'])
            ->findAll();
        $cnt_s = count($select_result);

        if ($cnt_f > 0 && $cnt_s <= 0) :
            $result['rst'] = 'ok';
            $result['rst_msg'] = '탈퇴 성공';
        else:
            $result['rst'] = 'fail';
            $result['rst_msg'] = '탈퇴 실패';
        endif;

        return json_encode($result);
    }
}