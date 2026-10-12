import React from 'react'
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormRule from './FormRule';

interface TableRulesProps extends TableProps {
  idPriceList?: number,
}

const TableRules = (props: TableRulesProps) => {
  return <Table
    componentName='TableRules'
    parentApp='Hubleto/App/Custom/PriceList'
    model='Hubleto/App/Custom/PriceList/Models/Rule'
    endpointParams={{idPriceList: props.idPriceList}}
    baseUrlSlug='price-lists/rules'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_price_list: props.idPriceList}}
    renderForm={(table: TableMeta) => <FormRule {...table.getDefaultFormProps()} /> }
    {...props}
  ></Table>
}

export default TableRules;
