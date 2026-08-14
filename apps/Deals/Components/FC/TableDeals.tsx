import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormDeal, { FormDealProps } from './FormDeal';

interface TableDealsProps extends TableProps {
  idCustomer?: number,
}

const componentName = 'TableDeals'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Deals';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableDeals = (props: TableDealsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Deal'}
    endpointParams={{idCustomer: props.idCustomer}}
    formUrlSlug='deals'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_customer: props.idCustomer}}
    getRowClassName={(table: TableMeta, rowData: any): string => {
      return rowData.is_closed ? 'bg-slate-300' : table.getDefaultRowClassName(rowData);
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormDeal {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableDeals;
