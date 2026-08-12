import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormActivityType, { FormActivityTypeProps } from './FormActivityType';

interface TableActivityTypesProps extends TableProps {
  // Delete or change, if your table shall be filterable
  // by some field. Check prepareReadQuery() in model's
  // record manager if appropriate filtering is applied.
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
    formUrlSlug='parent-app-slug/same-url-slug-as-in-form'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_some_field: props.idSomeField}}
    // getRowClassName={(table: TableMeta, rowData: any): string => { return table.getDefaultRowClassName(rowData); }}
    // renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => { return table.renderDefaultCell(columnName, column, data, options); }}
    // renderActionsColumn={(table: TableMeta, row: any) => { return table.renderDefaultActionsColumn(row); }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormActivityType {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableActivityTypes;


// import React, { Component } from 'react'
// import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/components/cc/TableExtended';
// import FormActivityType from './FormActivityType';

// interface TableActivityTypesProps extends TableExtendedProps {
//   // Uncomment and modify these lines if you want to create URL-based filtering for your model
//   // idCustomer?: number,
// }

// interface TableActivityTypesState extends TableExtendedState {
// }

// export default class TableActivityTypes extends TableExtended<TableActivityTypesProps, TableActivityTypesState> {
//   static defaultProps = {
//     ...TableExtended.defaultProps,
//     formUseModalSimple: true,
//     model: 'Hubleto/App/Community/Worksheets/Models/ActivityType',
//   }

//   props: TableActivityTypesProps;
//   state: TableActivityTypesState;

//   translationContext: string = 'Hubleto\\App\\Community\\Worksheets\\Loader';
//   translationContextInner: string = 'Components\\TableActivityTypes';

//   constructor(props: TableActivityTypesProps) {
//     super(props);
//     this.state = this.getStateFromProps(props);
//   }

//   getStateFromProps(props: TableActivityTypesProps) {
//     return {
//       ...super.getStateFromProps(props),
//     }
//   }

//   getFormModalProps(): any {
//     let params = super.getFormModalProps();
//     params.type = 'right wide';
//     return params;
//   }

//   getEndpointParams(): any {
//     return {
//       ...super.getEndpointParams(),
//       // Uncomment and modify these lines if you want to create URL-based filtering for your model
//       // idCustomer: this.props.idCustomer,
//     }
//   }

//   renderForm(): React.JSX.Element {
//     let formProps = this.getFormProps();
//     // formProps.customEndpointParams.idCustomer = this.props.idCustomer;
//     // if (!formProps.description) formProps.description = {};
//     // formProps.description.defaultValues = { ...formProps.description.defaultValues ?? {}, id_customer: this.props.idCustomer };
//     return <FormActivityType {...formProps}/>;
//   }
// }