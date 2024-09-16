<?php
namespace Sqnbcadmin\Model;

use Think\Model;

/**
 * 后台贷款信息管理
 * @author mfy liniukeji.com
 *
 */
class LoanModel extends Model {


    protected $_validate = array(

        array('name', '4,200', '贷款名称不正确, 请输入4到200位字符', self::MUST_VALIDATE, 'length', 3),
        array('click_count', 'number', '查看次数数据不合法', self::MUST_VALIDATE, 'function', 3),
        array('approval_cycle', 'number', '审批周期数据不合法', self::VALUE_VALIDATE, 'function', 3),
        array('term_loan_start', 'number', '借款期限开始数据不合法', self::VALUE_VALIDATE, 'function', 3),
        array('term_loan_end', 'number', '借款期限结束数据不合法', self::VALUE_VALIDATE, 'function', 3),

        array('amount_start', 'number', '借款金额开始数据不合法', self::VALUE_VALIDATE, 'function', 3),
        array('amount_end', 'number', '借款金额结束数据不合法', self::VALUE_VALIDATE, 'function', 3),

        array('type_id', 'number', '借款类型数据不合法', self::VALUE_VALIDATE, 'function', 3),


    );

    protected function _before_insert(&$data, $option) {
        $data['add_time'] = NOW_TIME;
        unset($data['id']);
    }

    protected function _before_update(&$data, $option) {
        $data['add_time'] = NOW_TIME;
    }

    // 获取贷款产品信息
    public function getLoanList($where,$order='loan.add_time desc, loan.id asc '){

        $keywords = I('keywords', '');

        if ($keywords) {
            $where['loan.name'] = array('like','%'.$keywords.'%');
        }

        $count = $this->alias('loan')->join('__LOAN_TYPE__ loan_type on loan.type_id = loan_type.id','left')->where($where)->count();

        $page = get_page($count);

        $loanList = $this->alias('loan')
                    ->field('loan.*,loan_type.name as typename')
                        ->join('__LOAN_TYPE__ loan_type on loan.type_id = loan_type.id','left')
                            ->where($where)
                                ->limit($page['limit'])
                                    ->order($order)
                                        ->select();

        return array('list' => $loanList,'page' => $page);

    }


}
