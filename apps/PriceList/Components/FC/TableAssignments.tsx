import React from 'react'
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormAssignment from './FormAssignment';

interface TableAssignmentsProps extends TableProps {
  idPriceList?: number,
  idCustomer?: number,
  idShop?: number,
  idProject?: number,
  idDeal?: number,
  idSupplier?: number,
}

const TableAssignments = (props: TableAssignmentsProps) => {
  return <Table
    componentName='TableAssignments'
    parentApp='Hubleto/App/Custom/PriceList'
    model='Hubleto/App/Custom/PriceList/Models/Assignment'
    endpointParams={{idPriceList: props.idPriceList, idCustomer: props.idCustomer, idShop: props.idShop, idProject: props.idProject, idDeal: props.idDeal, idSupplier: props.idSupplier}}
    baseUrlSlug='price-lists/assignments'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_price_list: props.idPriceList, id_customer: props.idCustomer, id_shop: props.idShop, id_project: props.idProject, id_deal: props.idDeal, id_supplier: props.idSupplier}}
    renderForm={(table: TableMeta) => <FormAssignment {...table.getDefaultFormProps()} /> }
    {...props}
  ></Table>
}

export default TableAssignments;
