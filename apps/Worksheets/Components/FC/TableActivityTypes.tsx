import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormActivityType from './FormActivityType';

interface TableActivityTypesProps extends TableProps {
  idSomeField?: number,
}

const componentName = 'TableActivityTypes'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Worksheets';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableActivityTypes = (props: TableActivityTypesProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/XXX'}
    endpointParams={{idSomeField: props.idSomeField}}
    baseUrlSlug='parent-app-slug/same-url-slug-as-in-form'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_some_field: props.idSomeField}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormActivityType {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableActivityTypes;
