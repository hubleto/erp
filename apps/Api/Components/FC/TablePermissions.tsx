import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormPermission from './FormPermission';

interface TablePermissionsProps extends TableProps {
  // Delete or change, if your table shall be filterable
  // by some field. Check prepareReadQuery() in model's
  // record manager if appropriate filtering is applied.
  idKey?: number,
}

const componentName = 'TablePermissions'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Api';

const TablePermissions = (props: TablePermissionsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Permission'}
    endpointParams={{idKey: props.idKey}}
    formUrlSlug='api/permissions'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_key: props.idKey}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormPermission {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TablePermissions;
