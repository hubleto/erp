import React, { Component } from 'react';
import { deepObjectMerge, getUrlParam } from 'adios/Helper';
import Form, { FormProps, FormState } from 'adios/Form';
import InputTags2 from 'adios/Inputs/Tags2';
import InputTable from 'adios/Inputs/Table';
import FormInput from 'adios/FormInput';
import TableRolePermissions from './TableRolePermissions';

interface FormUserRolesProps extends FormProps {}

interface FormUserRolesState extends FormState {}

export default class FormUserRoles<P, S> extends Form<FormUserRolesProps,FormUserRolesState> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: 'CeremonyCrmApp/Modules/Core/Settings/Models/UserRole',
  };

  props: FormUserRolesProps;
  state: FormUserRolesState;

  translationContext: string = 'mod.core.settings.formUserRole';

  constructor(props: FormUserRolesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormUserRolesProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  /* normalizeRecord(record) {

    return record;
  } */

  renderHeaderLeft(): JSX.Element {
    return <>
      {this.state.isInlineEditing ? this.renderSaveButton() : this.renderEditButton()}
    </>;
  }

  renderHeaderRight(): JSX.Element {
    return <>
      {this.state.isInlineEditing ? this.renderDeleteButton() : null}
      {this.props.showInModal ? this.renderCloseButton() : null}
    </>;
  }

  renderTitle(): JSX.Element {
    if (getUrlParam('recordId') == -1) {
      return(
        <>
          <h2>
            {'New User Role'}
          </h2>
        </>
      );
    } else {
      return (
        <>
          <h2>
            {this.state.record.role
              ? this.state.record.role
              : '[Undefined Name]'}
          </h2>
        </>
      );
    }
  }

  /* onBeforeSaveRecord(record: any) {

    return record;
  } */

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;

    return (
      <>
        <div className='grid grid-cols-2 gap-1' style=
          {{gridTemplateAreas:`
            'info info'
            'permissions permissions'
          `}}>
            <div className='card mt-4' style={{gridArea: 'info'}}>
              <div className='card-body'>
                {this.inputWrapper('role')}
              </div>
            </div>

            <div className='card mt-2' style={{gridArea: 'permissions'}}>
              <div className='card-header'>Permissions</div>
              <div className='card-body'>
                {this.inputWrapper('grant_all')}
                {R.grant_all == 1 ? null :
                  <InputTable
                    uid={this.props.uid + '_table_permissions_steps_input'}
                    {...this.getDefaultInputProps()}
                    value={R.PERMISSIONS}
                    onChange={(value: any) => {
                      this.updateRecord({ PERMISSIONS: value });
                    }}
                  >
                    <TableRolePermissions
                      uid={this.props.uid + '_permissions_steps'}
                      context="Hello World"
                      descriptionSource="props"
                      description={{
                        ui: {
                          showFooter: false,
                          showHeader: false
                        },
                        permissions: {
                          canCreate: true,
                          canDelete: true,
                          canRead: true,
                          canUpdate: true
                        },
                        columns: {
                          id_permission: {
                            type: "lookup",
                            title: "Permission",
                            model: "CeremonyCrmApp/Modules/Core/Settings/Models/Permission",
                          },
                        }
                      }}
                    ></TableRolePermissions>
                  </InputTable>
                }
                {this.state.isInlineEditing && R.grant_all == 0 ? (
                  <a
                    role='button'
                    onClick={() => {
                      if (!R.PERMISSIONS) R.PERMISSIONS = [];
                      R.PERMISSIONS.push({
                        id_role: { _useMasterRecordId_: true },
                      });
                      this.setState({ record: R });
                    }}
                  >
                    + Add Permission
                  </a>
                ) : null}
              </div>
            </div>
        </div>
      </>
    );
  }
}
