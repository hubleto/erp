import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';

interface TableItemsProps extends TableProps {
  idDeal?: number,
}

const componentName = 'TableItems'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Deals';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableItems = (props: TableItemsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Item'}
    endpointParams={{idDeal: props.idDeal}}
    formUrlSlug='parent-app-slug/same-url-slug-as-in-form'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_deal: props.idDeal}}
    {...props}
  ></Table>
}

export default TableItems;
